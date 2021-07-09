<?php

namespace App\Qyweixin\Controllers;

/**
 * 应用授权
 */
class ApplicationsnsController extends ControllerBase
{
    // 活动ID
    protected $activity_id = 1;

    /**
     *
     * @var \App\Qyweixin\Models\User\User
     */
    private $modelQyweixinUser;

    // /**
    // *
    // * @var \App\Qyweixin\Models\Provider\Provider
    // */
    // private $modelQyweixinProvider;

    /**
     *
     * @var \App\Qyweixin\Models\Authorize\Authorizer
     */
    private $modelQyweixinAuthorizer;

    /**
     *
     * @var \App\Qyweixin\Models\Agent\Agent
     */
    private $modelQyweixinAgent;

    /**
     *
     * @var \App\Qyweixin\Models\ScriptTracking
     */
    private $modelQyweixinScriptTracking;

    /**
     *
     * @var \App\Qyweixin\Models\Callbackurls
     */
    private $modelQyweixinCallbackurls;

    /**
     *
     * @var \App\Qyweixin\Models\SnsApplication
     */
    private $modelQyweixinSnsApplication;

    // lock key
    private $lock_key_prefix = 'qyweixin_application_sns_';

    private $cookie_session_key = 'qyweixin_application_sns_';

    private $sessionKey;

    private $trackingKey = "授权应用SNS授权";

    private $appid;

    private $appConfig;

    private $provider_appid;

    private $providerConfig;

    private $authorizer_appid;

    private $authorizerConfig;

    private $agentid = 0;

    private $scope;

    private $state;

    // 应用类型 1:企业号
    private $app_type = 0;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        $this->modelQyweixinUser = new \App\Qyweixin\Models\User\User();
        // $this->modelQyweixinProvider = new \App\Qyweixin\Models\Provider\Provider();
        $this->modelQyweixinAuthorizer = new \App\Qyweixin\Models\Authorize\Authorizer();
        $this->modelQyweixinAgent = new \App\Qyweixin\Models\Agent\Agent();
        $this->modelQyweixinScriptTracking = new \App\Qyweixin\Models\ScriptTracking();
        $this->modelQyweixinCallbackurls = new \App\Qyweixin\Models\Callbackurls();
        $this->modelQyweixinSnsApplication = new \App\Qyweixin\Models\SnsApplication();
    }

    /**
     * 构造网页授权链接
     * 如果企业需要在打开的网页里面携带用户的身份信息，第一步需要构造如下的链接来获取code参数：
     *
     * https://open.weixin.qq.com/connect/oauth2/authorize?appid=CORPID&redirect_uri=REDIRECT_URI&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect
     * 参数说明：
     *
     * 参数 必须 说明
     * appid 是 企业的CorpID
     * redirect_uri 是 授权后重定向的回调链接地址，请使用urlencode对链接进行处理
     * response_type 是 返回类型，此时固定为：code
     * scope 是 应用授权作用域。企业自建应用固定填写：snsapi_base
     * state 否 重定向后会带上state参数，企业可以填写a-zA-Z0-9的参数值，长度不可超过128个字节
     * #wechat_redirect 是 终端使用此参数判断是否需要带上身份信息
     *
     * 示例：
     *
     * 假定当前
     * 企业CorpID：wxCorpId
     * 访问链接：http://api.3dept.com/cgi-bin/query?action=get
     * 根据URL规范，将上述参数分别进行UrlEncode，得到拼接的OAuth2链接为：
     * https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxCorpId&redirect_uri=http%3a%2f%2fapi.3dept.com%2fcgi-bin%2fquery%3faction%3dget&response_type=code&scope=snsapi_base&state=#wechat_redirect
     * 员工点击后，页面将跳转至
     * http://api.3dept.com/cgi-bin/query?action=get&code=AAAAAAgG333qs9EdaPbCAP1VaOrjuNkiAZHTWgaWsZQ&state=
     * 企业可根据code参数调用获得员工的userid
     * 注意到，构造OAuth2链接中参数的redirect_uri是经过UrlEncode的
     */
    public function authorizeAction()
    {
        // http://www.myapplicationmodule.com/qyweixin/api/applicationsns/authorize?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_base&refresh=1
        // http://www.myapplicationmodule.com/qyweixin/api/applicationsns/authorize?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_base&refresh=1
        // http://www.myapplicationmodule.com/qyweixin/api/applicationsns/authorize?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_base&refresh=1
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            $this->trackingKey = $this->trackingKey . "_网页授权";
            // 初始化
            $this->doInitializeLogic();

            $redirect = isset($_GET['redirect']) ? (trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址

            // $dc = isset($_GET['dc']) ? intval($_GET['dc']) : 1; // 是否检查回调域名
            $dc = empty($this->appConfig['is_cb_url_check']) ? 0 : 1; // 是否检查回调域名

            $refresh = isset($_GET['refresh']) ? intval($_GET['refresh']) : 0; // 是否刷新

            if ($dc) {
                // 添加重定向域的检查
                $isValid = $this->modelQyweixinCallbackurls->isValid($this->authorizer_appid, $this->provider_appid, $this->agentid, $redirect);
                if (empty($isValid)) {
                    throw new \Exception("回调地址不合法");
                }
            }

            if (!$refresh && !empty($_SESSION[$this->sessionKey])) {
                $arrAccessToken = $_SESSION[$this->sessionKey];
                $redirect = $this->getRedirectUrl4Sns($redirect, $arrAccessToken);
                $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['qyuserid'], $this->appConfig['_id']);
                header("location:{$redirect}");
                exit();
            } else {
                // 存储跳转地址
                $_SESSION['redirect'] = $redirect;
                $_SESSION['state'] = $this->state;
                $_SESSION['appid'] = $this->appid;
                $_SESSION['trackingKey'] = $this->trackingKey;

                $moduleName = 'qyweixin';
                $controllerName = $this->controllerName;
                $scheme = $this->getRequest()->getScheme();
                $redirectUri = $scheme . '://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/' . $moduleName;
                $redirectUri .= '/' . $controllerName;
                $redirectUri .= '/callback';

                // 授权处理
                // 应用类型 1:企业号
                if (empty($this->authorizerConfig['provider_appid']) && $this->app_type == \App\Qyweixin\Models\Authorize\Authorizer::APPTYPE_QY) {
                    $objSns = new \Qyweixin\Token\Sns($this->authorizer_appid, $this->authorizerConfig['appsecret']);
                } else {
                    throw new \Exception('该运用不支持授权操作');
                }
                $objSns->setState($this->state);
                $objSns->setRedirectUri($redirectUri);
                $redirectUri = $objSns->getQrConnectUrl($this->agentid, false);
                header("location:{$redirectUri}");
                exit();
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 构造扫码登录链接
     * 构造独立窗口登录二维码
     * 构造内嵌登录二维码
     * 步骤一：引入JS文件
     * 步骤二：在需要使用微信登录的地方实例JS对象
     * 构造独立窗口登录二维码
     * 开发者需要构造如下的链接来获取code参数：
     *
     * https://open.work.weixin.qq.com/wwopen/sso/qrConnect?appid=CORPID&agentid=AGENTID&redirect_uri=REDIRECT_URI&state=STATE
     * 参数说明
     *
     * 参数 必须 说明
     * appid 是 企业微信的CorpID，在企业微信管理端查看
     * agentid 是 授权方的网页应用ID，在具体的网页应用中查看
     * redirect_uri 是 重定向地址，需要进行UrlEncode
     * state 否 用于保持请求和回调的状态，授权请求后原样带回给企业。该参数可用于防止csrf攻击（跨站请求伪造攻击），建议企业带上该参数，可设置为简单的随机数加session进行校验
     * 若提示“该链接无法访问”，请检查参数是否填写错误，如redirect_uri的域名与网页应用的可信域名不一致
     *
     * 返回说明
     * 用户允许授权后，将会重定向到redirect_uri的网址上，并且带上code和state参数
     *
     * redirect_uri?code=CODE&state=STATE
     *
     * 若用户禁止授权，则重定向后不会带上code参数，仅会带上state参数
     *
     * redirect_uri?state=STATE
     *
     * 示例：
     *
     * 假定当前
     * 企业CorpID：wxCorpId
     * 开启授权登录的应用ID：1000000
     * 登录跳转链接：http://api.3dept.com
     * state设置为：weblogin@gyoss9
     * 需要配置的授权回调域为：api.3dept.com
     * 根据URL规范，将上述参数分别进行UrlEncode，得到拼接的OAuth2链接为：
     * https://open.work.weixin.qq.com/wwopen/sso/qrConnect?appid=wxCorpId&agentid=1000000&redirect_uri=http%3A%2F%2Fapi.3dept.com&state=web_login%40gyoss9
     */
    public function ssoqrconnectAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/applicationsns/ssoqrconnect?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&refresh=1
        // http://www.myapplicationmodule.com/qyweixin/api/applicationsns/ssoqrconnect?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&refresh=1
        // http://www.myapplicationmodule.com/qyweixin/api/applicationsns/ssoqrconnect?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&refresh=1
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            $this->trackingKey = $this->trackingKey . "_扫码登录";
            // 初始化
            $this->doInitializeLogic();
            if (empty($this->agentid)) {
                throw new \Exception("agentid未指定");
            }
            $redirect = isset($_GET['redirect']) ? (trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址

            // $dc = isset($_GET['dc']) ? intval($_GET['dc']) : 1; // 是否检查回调域名
            $dc = empty($this->appConfig['is_cb_url_check']) ? 0 : 1; // 是否检查回调域名

            $refresh = isset($_GET['refresh']) ? intval($_GET['refresh']) : 0; // 是否刷新

            if ($dc) {
                // 添加重定向域的检查
                $isValid = $this->modelQyweixinCallbackurls->isValid($this->authorizer_appid, $this->provider_appid, $this->agentid, $redirect);
                if (empty($isValid)) {
                    throw new \Exception("回调地址不合法");
                }
            }

            if (!$refresh && !empty($_SESSION[$this->sessionKey])) {
                $arrAccessToken = $_SESSION[$this->sessionKey];
                $redirect = $this->getRedirectUrl4Sns($redirect, $arrAccessToken);
                $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['qyuserid'], $this->appConfig['_id']);
                header("location:{$redirect}");
                exit();
            } else {
                // 存储跳转地址
                $_SESSION['redirect'] = $redirect;
                $_SESSION['state'] = $this->state;
                $_SESSION['appid'] = $this->appid;
                $_SESSION['trackingKey'] = $this->trackingKey;

                $moduleName = 'qyweixin';
                $controllerName = $this->controllerName;
                $scheme = $this->getRequest()->getScheme();
                $redirectUri = $scheme . '://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/' . $moduleName;
                $redirectUri .= '/' . $controllerName;
                $redirectUri .= '/callback';

                // 授权处理
                // 应用类型 1:企业号
                if (empty($this->authorizerConfig['provider_appid']) && $this->app_type == \App\Qyweixin\Models\Authorize\Authorizer::APPTYPE_QY) {
                    $objSns = new \Qyweixin\Token\Sns($this->authorizer_appid, $this->authorizerConfig['appsecret']);
                } else {
                    throw new \Exception('该运用不支持授权操作');
                }
                $objSns->setScope($this->scope);
                $objSns->setState($this->state);
                $objSns->setRedirectUri($redirectUri);
                $redirectUri = $objSns->getAuthorizeUrl(false);
                header("location:{$redirectUri}");
                exit();
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 员工点击后，页面将跳转至 redirect_uri?code=CODE&state=STATE，企业可根据code参数获得员工的userid。code长度最大为512字节。
     */
    public function callbackAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/applicationsns/callback?code=xxx&scope=auth_user&state=xxx
        try {
            $this->trackingKey = empty($_SESSION['trackingKey']) ? "" : $_SESSION['trackingKey'];

            $appid = empty($_SESSION['appid']) ? "" : $_SESSION['appid'];
            if (empty($appid)) {
                throw new \Exception("appid未定义");
            }
            $_GET['appid'] = $appid;

            // 初始化
            $this->doInitializeLogic();

            $code = isset($_GET['code']) ? ($_GET['code']) : '';
            if (empty($code)) {
                // 如果用户未授权登录，点击取消，自行设定取消的业务逻辑
                throw new \Exception("点击取消,用户未授权登录");
            }
            $redirect = empty($_SESSION['redirect']) ? "" : $_SESSION['redirect'];
            if (empty($redirect)) {
                throw new \Exception("回调地址未定义");
            }

            $state = empty($_SESSION['state']) ? "" : $_SESSION['state'];
            if ($state != $this->state) {
                throw new \Exception("state发生了改变");
            }

            $updateInfoFromWx = true;
            $sourceFromUserName = !empty($_GET['FromUserName']) ? $_GET['FromUserName'] : '';

            // 授权成功后，记录该企业微信用户的基本信息
            // 应用类型 1:企业号
            if ($this->app_type != \App\Qyweixin\Models\Authorize\Authorizer::APPTYPE_QY) {
                throw new \Exception('该运用不支持授权操作');
            }

            $objSns = new \Qyweixin\Token\Sns($this->authorizer_appid, $this->authorizerConfig['appsecret']);
            if (empty($this->agentid)) {
                $access_token = $this->authorizerConfig['access_token'];
            } else {
                $agentInfo = $this->modelQyweixinAgent->getInfoByAppid($this->provider_appid, $this->authorizer_appid, $this->agentid);
                $access_token = $agentInfo['access_token'];
            }

            $arrAccessToken = $objSns->getUserInfo($access_token);
            if (!empty($arrAccessToken['errcode'])) {
                throw new \Exception("获取token失败,原因:" . json_encode($arrAccessToken, JSON_UNESCAPED_UNICODE));
            }
            $arrAccessToken['scope'] = $this->scope;
            $arrAccessToken['access_token'] = $access_token;
            $arrAccessToken['refresh_token'] = "";

            $userInfoAndAccessTokenRet = $this->getUserInfo4AccessToken($arrAccessToken);
            $arrAccessToken = $userInfoAndAccessTokenRet['arrAccessToken'];
            $userInfo = $userInfoAndAccessTokenRet['userInfo'];

            if (!empty($userInfo)) {
                if (!empty($userInfo['name'])) {
                    $arrAccessToken['name'] = ($userInfo['name']);
                }

                if (!empty($userInfo['avatar'])) {
                    $arrAccessToken['avatar'] = stripslashes($userInfo['avatar']);
                }

                if (!empty($userInfo['unionid'])) {
                    $arrAccessToken['unionid'] = ($userInfo['unionid']);
                }
            }

            $_SESSION[$this->sessionKey] = $arrAccessToken;
            $redirect = $this->getRedirectUrl4Sns($redirect, $arrAccessToken);

            if ($sourceFromUserName !== null && $sourceFromUserName == $arrAccessToken['qyuserid']) {
                $redirect = $this->addUrlParameter($redirect, array(
                    '__self' => true
                ));
            }

            // 调整数据库操作的执行顺序，优化跳转速度
            if ($updateInfoFromWx) {
                if (!empty($userInfo['avatar'])) {
                    $userInfo['avatar'] = stripslashes($userInfo['avatar']);
                }
                if (!empty($arrAccessToken['userid'])) {
                    $lock = new \iLock($this->lock_key_prefix . $arrAccessToken['userid'] . $this->authorizer_appid . $this->provider_appid);
                    if (!$lock->lock()) {
                        $this->modelQyweixinUser->updateUserInfoBySns($arrAccessToken['userid'], $this->authorizer_appid, $this->provider_appid, $userInfo);
                    }
                }
            }
            $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['qyuserid'], $this->appConfig['_id']);
            header("location:{$redirect}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    protected function addUrlParameter($url, array $params)
    {
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (strpos($url, $key) === false || ($key == "FromUserName")) {
                    if (strpos($url, '?') === false)
                        $url .= "?{$key}=" . $value;
                    else
                        $url .= "&{$key}=" . $value;
                }
            }
        }
        return $url;
    }

    protected function getSignKey($openid, $timestamp = 0)
    {
        return $this->modelQyweixinSnsApplication->getSignKey($openid, $this->appConfig['secretKey'], $timestamp);
    }

    protected function getRedirectUrl4Sns($redirect, $arrAccessToken)
    {
        // $redirect = $this->addUrlParameter($redirect, array(
        // 'it_appid' => $this->appid
        // ));
        // if ($this->app_type != \App\Qyweixin\Models\Authorize\Authorizer::APPTYPE_QY) {
        // $redirect = $this->addUrlParameter($redirect, array(
        // 'it_userToken' => urlencode($arrAccessToken['access_token'])
        // ));

        // $redirect = $this->addUrlParameter($redirect, array(
        // 'it_refreshToken' => urlencode($arrAccessToken['refresh_token'])
        // ));
        // }
        $redirect = $this->addUrlParameter($redirect, array(
            'it_openid' => $arrAccessToken['openid']
        ));
        $redirect = $this->addUrlParameter($redirect, array(
            'it_userid' => $arrAccessToken['userid']
        ));

        // 计算signkey
        $timestamp = time();
        $signkey = $this->getSignKey($arrAccessToken['userid'] . "|" . $arrAccessToken['openid'], $timestamp);
        $redirect = $this->addUrlParameter($redirect, array(
            'it_signkey' => $signkey
        ));
        $redirect = $this->addUrlParameter($redirect, array(
            'it_timestamp' => $timestamp
        ));

        if (!empty($arrAccessToken['name'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                'it_name' => urlencode($arrAccessToken['name'])
            ));
        }

        if (!empty($arrAccessToken['avatar'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                'it_avatar' => urlencode(stripslashes($arrAccessToken['avatar']))
            ));
        }

        // if (!empty($arrAccessToken['unionid'])) {
        //     $redirect = $this->addUrlParameter($redirect, array(
        //         'it_unionid' => $arrAccessToken['unionid']
        //     ));
        //     $signkey = $this->getSignKey($arrAccessToken['unionid'], $timestamp);
        //     $redirect = $this->addUrlParameter($redirect, array(
        //         'it_signkey2' => $signkey
        //     ));
        // }

        return $redirect;
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        // 应用ID
        $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : "";
        if (empty($this->appid)) {
            throw new \Exception("appid为空");
        }
        $this->appConfig = $this->modelQyweixinSnsApplication->getInfoByAppid($this->appid);
        if (empty($this->appConfig)) {
            throw new \Exception("appid:{$this->appid}所对应的记录不存在");
        }

        $isValid = $this->modelQyweixinSnsApplication->checkIsValid($this->appConfig, $this->now);
        if (empty($isValid)) {
            throw new \Exception("appid:{$this->appid}所对应的记录已无效");
        }
        // 第三方服务商运用ID
        $this->provider_appid = $this->appConfig['provider_appid'];
        // if (empty($this->provider_appid)) {
        // throw new \Exception("provider_appid为空");
        // }
        // $this->providerConfig = $this->modelQyweixinProvider->getInfoByAppid($this->provider_appid);
        // if (empty($this->providerConfig)) {
        // throw new \Exception("provider_appid:{$this->provider_appid}所对应的记录不存在");
        // }

        // 授权方ID
        $this->authorizer_appid = $this->appConfig['authorizer_appid'];
        if (empty($this->authorizer_appid)) {
            throw new \Exception("authorizer_appid为空");
        }
        $this->authorizerConfig = $this->modelQyweixinAuthorizer->getInfoByAppid($this->provider_appid, $this->authorizer_appid);
        if (empty($this->authorizerConfig)) {
            throw new \Exception("provider_appid:{$this->provider_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
        }
        // 应用类型 1:企业号
        $this->app_type = intval($this->authorizerConfig['app_type']);
        $this->agentid = empty($this->appConfig['agentid']) ? 0 : $this->appConfig['agentid'];
        if (empty($this->agentid)) {
            throw new \Exception("agentid未指定");
        }
        $this->state = isset($_GET['state']) ? trim($_GET['state']) : uniqid();
        $this->scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'snsapi_base';
        $this->sessionKey = $this->cookie_session_key . "_accessToken_{$this->appid}_{$this->provider_appid}_{$this->authorizer_appid}_{$this->agentid}_{$this->scope}";
    }

    protected function getUserInfo4AccessToken($arrAccessToken)
    {
        // a) 当用户为企业成员时
        // UserId 成员UserID。若需要获得用户详情信息，可调用通讯录接口：读取成员
        // DeviceId 手机设备号(由企业微信在安装时随机生成，删除重装会改变，升级不受影响)
        if (isset($arrAccessToken['UserId'])) {
            $arrAccessToken['openid'] = '';
            $arrAccessToken['userid'] = $arrAccessToken['UserId'];
            $arrAccessToken['is_qy_member'] = 1;
            $arrAccessToken['qyuserid'] = $arrAccessToken['userid'];
            // // 先判断用户在数据库中是否存在最近一周产生的openid，如果不存在，则再动用网络请求，进行用户信息获取
            // $userInfo = $this->modelQyweixinUser->getUserInfoByIdLastWeek($arrAccessToken['userid'], $this->authorizer_appid, $this->provider_appid, $this->now);
            // if (true || empty($userInfo)) {
            // $updateInfoFromWx = true;
            // $weixin = new \Qyweixin\Client($this->authorizer_appid, $this->authorizerConfig['appsecret']);
            // $weixin->setAccessToken($arrAccessToken['access_token']);
            // $userInfo = $weixin->getUserManager()->get($arrAccessToken['userid']);
            // if (!empty($userInfo['errcode'])) {
            // throw new \Exception("获取用户信息失败，原因:" . json_encode($userInfo, JSON_UNESCAPED_UNICODE));
            // }
            // }
        } // b) 非企业成员授权时
        // OpenId 非企业成员的标识，对当前企业唯一
        // DeviceId 手机设备号(由企业微信在安装时随机生成，删除重装会改变，升级不受影响)
        elseif (isset($arrAccessToken['OpenId'])) {
            $arrAccessToken['userid'] = '';
            $arrAccessToken['openid'] = $arrAccessToken['OpenId'];
            $arrAccessToken['is_qy_member'] = 0;
            $arrAccessToken['qyuserid'] = $arrAccessToken['openid'];
        }

        $userInfo = array();
        $userInfo['userid'] = $arrAccessToken['userid'];
        $userInfo['openid'] = $arrAccessToken['openid'];
        $userInfo['access_token'] = array_merge($arrAccessToken, $userInfo);

        return array('arrAccessToken' => $arrAccessToken, 'userInfo' => $userInfo);
    }
}
