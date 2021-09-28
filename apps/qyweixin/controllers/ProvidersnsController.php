<?php

namespace App\Qyweixin\Controllers;

/**
 * 代公众号发起网页授权
 * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318590&token=&lang=
 */
class ComponentsnsController extends ControllerBase
{

    // 活动ID
    protected $activity_id = 1;

    /**
     *
     * @var \App\Qyweixin\Models\Contact\User
     */
    private $modelQyweixinUser;

    /**
     * @var \App\Qyweixin\Models\Provider\Provider
     */
    private $modelQyweixinProvider;

    /**
     * @var \App\Qyweixin\Models\Authorize\Authorizer
     */
    private $modelQyweixinAuthorizer;

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
    private $lock_key_prefix = 'qyweixin_component_sns_';

    private $cookie_session_key = 'qyweixin_component_sns_';

    private $sessionKey;

    private $trackingKey = "第三方服务商SNS授权";

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

        $this->modelQyweixinUser = new \App\Qyweixin\Models\Contact\User();
        $this->modelQyweixinProvider = new \App\Qyweixin\Models\Provider\Provider();
        $this->modelQyweixinAuthorizer = new \App\Qyweixin\Models\Authorize\Authorizer();
        $this->modelQyweixinScriptTracking = new \App\Qyweixin\Models\ScriptTracking();
        $this->modelQyweixinCallbackurls = new \App\Qyweixin\Models\Callbackurls();
        $this->modelQyweixinSnsApplication = new \App\Qyweixin\Models\SnsApplication();
    }

    /**
     * 构造第三方应用oauth2链接
     * 如果第三方应用需要在打开的网页里面携带用户的身份信息，第一步需要构造如下的链接来获取code：
     *
     * https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
     * 参数说明：
     *
     * 参数 必须 说明
     * appid 是 第三方应用id（即ww或wx开头的suite_id）。注意与企业的网页授权登录不同
     * redirect_uri 是 授权后重定向的回调链接地址，请使用urlencode对链接进行处理 ，注意域名需要设置为第三方应用的可信域名
     * response_type 是 返回类型，此时固定为：code
     * scope 是 应用授权作用域。
     * snsapi_base：静默授权，可获取成员的基础信息（UserId与DeviceId）；
     * snsapi_userinfo：静默授权，可获取成员的详细信息，但不包含手机、邮箱等敏感信息；
     * snsapi_privateinfo：手动授权，可获取成员的详细信息，包含手机、邮箱等敏感信息（已废弃）。
     * state 否 重定向后会带上state参数，企业可以填写a-zA-Z0-9的参数值，长度不可超过128个字节
     * #wechat_redirect 是 固定内容
     * 企业员工点击后，页面将跳转至 redirect_uri?code=CODE&state=STATE，第三方应用可根据code参数获得企业员工的corpid与userid。code长度最大为512字节。
     *
     * 权限说明：
     * 使用snsapi_privateinfo的scope时，第三方应用必须有“成员敏感信息授权”的权限。
     */
    public function authorizeAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/providersns/authorize?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_base&refresh=1
        // http://www.myapplicationmodule.com/qyweixin/api/providersns/authorize?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_base&refresh=1
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            $this->trackingKey = $this->trackingKey . "_第三方应用";
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
                $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['qyuserid']);
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
                $objSns = new \Qyweixin\Token\ServiceSns();
                $objSns->setAppid($this->authorizer_appid);
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
     * 构造企业oauth2链接
     * 如果企业需要在打开的网页里面携带用户的身份信息，第一步需要构造如下的链接来获取code参数：
     *
     * https://open.weixin.qq.com/connect/oauth2/authorize?appid=CORPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&agentid=AGENTID&state=STATE#wechat_redirect
     * 参数说明：
     *
     * 参数 必须 说明
     * appid 是 企业的CorpID
     * redirect_uri 是 授权后重定向的回调链接地址，请使用urlencode对链接进行处理
     * response_type 是 返回类型，此时固定为：code
     * scope 是 应用授权作用域。
     * snsapi_base：静默授权，可获取成员的的基础信息（UserId与DeviceId）；
     * snsapi_userinfo：静默授权，可获取成员的详细信息，但不包含手机、邮箱；
     * snsapi_privateinfo：手动授权，可获取成员的详细信息，包含手机、邮箱
     * 注意：企业自建应用可以根据userid获取成员详情，无需使用snsapi_userinfo和snsapi_privateinfo两种scope。更多说明见scope
     * agentid 否 企业应用的id。
     * 当scope是snsapi_userinfo或snsapi_privateinfo时，该参数必填
     * 注意redirect_uri的域名必须与该应用的可信域名一致。
     * state 否 重定向后会带上state参数，企业可以填写a-zA-Z0-9的参数值，长度不可超过128个字节
     * #wechat_redirect 是 终端使用此参数判断是否需要带上身份信息
     * 员工点击后，页面将跳转至 redirect_uri?code=CODE&state=STATE，企业可根据code参数获得员工的userid。code长度最大为512字节。
     */
    public function authorize4corpAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/providersns/authorize4corp?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_base&refresh=1
        // http://www.myapplicationmodule.com/qyweixin/api/providersns/authorize4corp?appid=4m9QOrJMzAjpx75Y&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_base&refresh=1
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            $this->trackingKey = $this->trackingKey . "_第三方应用企业";
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
                $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['qyuserid']);
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
                $objSns = new \Qyweixin\Token\ServiceSns();
                $objSns->setAppid($this->authorizer_appid);
                $objSns->setScope($this->scope);
                $objSns->setState($this->state);
                $objSns->setAgentid($this->agentid);
                $objSns->setRedirectUri($redirectUri);
                $redirectUri = $objSns->getAuthorizeUrl4Corp(false);
                header("location:{$redirectUri}");
                exit();
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 从第三方单点登录
     * 此功能可方便的让用户使用企业微信管理员或成员帐号登录第三方网站，该登录授权基于OAuth2.0协议标准构建。
     * 使用前，请登录服务商管理后台进行登录授权配置，如下图。
     *
     * 登录授权设置说明：
     *
     * 参数 说明
     * 登录授权发起域名 在该域名下发起的登录授权请求才可被通过，企业点击授权链接时，企业微信会检查该域名是否已登记
     * 授权完成回调域名 登录授权成功之后会回调到该域名下的URL，返回授权码和过期时间，开发者即可使用该授权码获取登录授权信息
     * 登录授权进入服务商网站流程：
     *
     *
     * 步骤说明：
     * 1、用户进入服务商网站
     * 2、服务商网站引导用户进入登录授权页
     * 服务商可以在自己的网站首页中放置“企业微信登录”的入口，引导用户进入登录授权页。网址为:
     *
     * https://open.work.weixin.qq.com/wwopen/sso/3rd_qrConnect?appid=ww100000a5f2191&redirect_uri=http%3A%2F%2Fwww.oa.com&state=web_login@gyoss9&usertype=admin
     * 参数说明：
     *
     * 参数 是否必须 说明
     * appid 是 服务商的CorpID
     * redirect_uri 是 授权登录之后目的跳转网址，需要做urlencode处理。所在域名需要与授权完成回调域名一致
     * state 否 用于企业或服务商自行校验session，防止跨域攻击
     * usertype 否 支持登录的类型。admin代表管理员登录（使用微信扫码）,member代表成员登录（使用企业微信扫码），默认为admin
     * 3、用户确认并同意授权
     * 用户进入登录授权页后，需要确认并同意将自己的企业微信和登录账号信息授权给企业或服务商，完成授权流程。
     * 4、授权后回调URI，得到授权码和过期时间
     * 授权流程完成后，会进入回调URI，并在URL参数中返回授权码，跳转地址
     *
     * redirect_url?auth_code=xxx
     * 5、利用授权码调用企业微信的相关API
     * 在得到登录授权码后，企业或服务商即可使用该授权码换取登录授权信息。
     */
    public function sso3rdqrConnectAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/providersns/sso3rdqrConnect?provider_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&usertype=admin&refresh=1
        // http://www.myapplicationmodule.com/qyweixin/api/providersns/sso3rdqrConnect?provider_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&usertype=admin&refresh=1
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            $this->trackingKey = $this->trackingKey . "_第三方单点登录";
            // 初始化
            $this->authorizer_appid = "";
            $this->agentid = 0;
            // 第三方服务商运用ID
            $this->provider_appid = isset($_GET['provider_appid']) ? trim($_GET['provider_appid']) : "";
            if (empty($this->provider_appid)) {
                throw new \Exception("provider_appid为空");
            }
            $this->providerConfig = $this->modelQyweixinProvider->getInfoByAppid($this->provider_appid);
            if (empty($this->providerConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}所对应的记录不存在");
            }
            $this->sessionKey = $this->cookie_session_key . "_accessToken_{$this->provider_appid}_{$this->authorizer_appid}_{$this->scope}";

            $usertype = isset($_GET['usertype']) ? (trim($_GET['usertype'])) : 'admin'; // 支持登录的类型
            $redirect = isset($_GET['redirect']) ? (trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
            $dc = isset($_GET['dc']) ? intval($_GET['dc']) : 1; // 是否检查回调域名
            $refresh = isset($_GET['refresh']) ? intval($_GET['refresh']) : 0; // 是否刷新

            if ($dc) {
                // // 添加重定向域的检查
                // $isValid = $this->modelQyweixinCallbackurls->isValid($this->authorizer_appid, $this->provider_appid, $this->agentid, $redirect);
                // if (empty($isValid)) {
                // throw new \Exception("回调地址不合法");
                // }
            }

            if (!$refresh && !empty($_SESSION[$this->sessionKey])) {
                $arrAccessToken = $_SESSION[$this->sessionKey];
                $redirect = $this->getRedirectUrl4Sns($redirect, $arrAccessToken);
                $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                header("location:{$redirect}");
                exit();
            } else {
                // 存储跳转地址
                $_SESSION['redirect'] = $redirect;
                $_SESSION['state'] = $this->state;
                $_SESSION['usertype'] = $usertype;
                $_SESSION['trackingKey'] = $this->trackingKey;

                $moduleName = 'qyweixin';
                $controllerName = $this->controllerName;
                $scheme = $this->getRequest()->getScheme();
                $redirectUri = $scheme . '://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/' . $moduleName;
                $redirectUri .= '/' . $controllerName;
                $redirectUri .= '/ssoauthcallback';
                $redirectUri .= '?provider_appid=' . $this->provider_appid;

                // 授权处理
                $objSns = new \Qyweixin\Token\ServiceSns();
                $objSns->setAppid($this->provider_appid);
                $objSns->setState($this->state);
                $objSns->setUserType($usertype);
                $objSns->setRedirectUri($redirectUri);
                $redirectUri = $objSns->getAuthorizeUrl4Sso(false);
                header("location:{$redirectUri}");
                exit();
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 企业员工点击后，页面将跳转至 redirect_uri?code=CODE&state=STATE，第三方应用可根据code参数获得企业员工的corpid与userid。code长度最大为512字节。
     */
    public function callbackAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/providersns/callback?appid=xxx&code=xxx&scope=auth_user&state=xxx
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

            // 创建service
            $qyService = new \App\Qyweixin\Services\QyService($this->authorizer_appid, $this->provider_appid, $this->agentid);
            $objQyProvider = $qyService->getQyweixinProvider();

            // 第二步：通过code获取访问用户身份
            $suite_access_token = $this->authorizerConfig['suite_access_token'];
            $arrAccessToken = $objQyProvider->getUserInfo3rd($suite_access_token, $code);
            if (!empty($arrAccessToken['errcode'])) {
                throw new \Exception("获取token失败,原因:" . \App\Common\Utils\Helper::myJsonEncode($arrAccessToken));
            }

            $arrAccessToken['scope'] = $this->scope;
            $arrAccessToken['access_token'] = $suite_access_token;
            $arrAccessToken['refresh_token'] = "";

            $userInfoAndAccessTokenRet = $this->getUserInfo4AccessToken($objQyProvider, $arrAccessToken);
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
                    $lock = new \iLock($this->lock_key_prefix . $arrAccessToken['openid'] . $this->authorizer_appid . $this->provider_appid);
                    if (!$lock->lock()) {
                        $this->modelQyweixinUser->updateUserInfoBySns($arrAccessToken['openid'], $this->authorizer_appid, $this->provider_appid, $userInfo);
                    }
                }
            }
            $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['qyuserid']);

            header("location:{$redirect}");
            exit();
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 从企业微信管理端单点登录
     * 企业微信管理员可从第三方应用的‘业务设置’入口跳转到第三方网站，流程是：
     *
     *
     * 步骤说明：
     * 1、管理员登录企业微信管理端，点击应用中的“业务设置”。目前仅有托管于服务商的应用有此入口。
     * 2、跳转到第三方服务商的业务设置URL，服务商据此得到登录授权码。假设”业务设置URL”为https://www.AAA.com, 那么跳转地址为:
     *
     * https://www.AAA.com?auth_code=xxx
     * 3、利用登录授权码调用相关API。在得到单点登录授权码后，第三方服务商可以使用该授权码换取登录授权信息。
     *
     * 注：使用该功能之前，服务商需要在第三方应用设置“业务设置”的链接。
     */
    public function ssoauthcallbackAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/providersns/login?provider_appid=wxca8519f703c07d32&auth_code=xxx
        try {
            $this->trackingKey = empty($_SESSION['trackingKey']) ? "" : $_SESSION['trackingKey'];
            // 初始化
            // 第三方服务商运用ID
            $this->provider_appid = isset($_GET['provider_appid']) ? trim($_GET['provider_appid']) : "";
            if (empty($this->provider_appid)) {
                throw new \Exception("provider_appid为空");
            }
            $this->providerConfig = $this->modelQyweixinProvider->getInfoByAppid($this->provider_appid);
            if (empty($this->providerConfig)) {
                throw new \Exception("provider_appid:{$this->provider_appid}所对应的记录不存在");
            }
            $this->agentid = 0;
            $this->authorizer_appid = "";
            $this->authorizerConfig['secretKey'] = $this->providerConfig['secretKey'];

            $auth_code = isset($_GET['auth_code']) ? ($_GET['auth_code']) : '';
            if (empty($auth_code)) {
                throw new \Exception("登录授权码未定义");
            }
            $redirect = empty($_SESSION['redirect']) ? "" : $_SESSION['redirect'];
            if (empty($redirect)) {
                // throw new \Exception("回调地址未定义");
            }
            $state = empty($_SESSION['state']) ? "" : $_SESSION['state'];
            if (!empty($state)) {
                if ($state != $this->state) {
                    throw new \Exception("state发生了改变");
                }
            }

            $updateInfoFromWx = true;
            $sourceFromUserName = !empty($_GET['FromUserName']) ? $_GET['FromUserName'] : '';

            // 创建service
            $qyService = new \App\Qyweixin\Services\QyService($this->authorizer_appid, $this->provider_appid, $this->agentid);
            $objQyProvider = $qyService->getQyweixinProvider();

            // 授权成功后，记录该微信用户的基本信息
            $provider_access_token = $this->providerConfig['access_token'];
            $arrAccessToken = $objQyProvider->getLoginInfo($provider_access_token, $auth_code);
            if (!empty($arrAccessToken['errcode'])) {
                throw new \Exception("获取token失败,原因:" . \App\Common\Utils\Helper::myJsonEncode($arrAccessToken));
            }

            $arrAccessToken['scope'] = $this->scope;
            $arrAccessToken['access_token'] = $provider_access_token;
            $arrAccessToken['refresh_token'] = "";
            $arrAccessToken['userid'] = $arrAccessToken['user_info']['userid'];
            $arrAccessToken['name'] = $arrAccessToken['user_info']['name'];
            $arrAccessToken['avatar'] = $arrAccessToken['user_info']['avatar'];

            $userInfoAndAccessTokenRet = $this->getUserInfo4AccessToken($objQyProvider, $arrAccessToken);
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
            $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['qyuserid']);
            if (empty($redirect)) {
                return $this->result("OK", $arrAccessToken);
            } else {
                header("location:{$redirect}");
                exit();
            }
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
        return $this->modelQyweixinAuthorizer->getSignKey($openid, $this->authorizerConfig['secretKey'], $timestamp);
    }

    protected function getRedirectUrl4Sns($redirect, $arrAccessToken)
    {
        // $redirect = $this->addUrlParameter($redirect, array(
        //     'it_userToken' => urlencode($arrAccessToken['access_token'])
        // ));

        // $redirect = $this->addUrlParameter($redirect, array(
        //     'it_refreshToken' => urlencode($arrAccessToken['refresh_token'])
        // ));

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
        if (empty($this->provider_appid)) {
            throw new \Exception("provider_appid为空");
        }
        $this->providerConfig = $this->modelQyweixinProvider->getInfoByAppid($this->provider_appid);
        if (empty($this->providerConfig)) {
            throw new \Exception("provider_appid:{$this->provider_appid}所对应的记录不存在");
        }

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

        $this->state = isset($_GET['state']) ? trim($_GET['state']) : uniqid();
        $this->scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'snsapi_userinfo';
        $this->sessionKey = $this->cookie_session_key . "_accessToken_{$this->provider_appid}_{$this->authorizer_appid}_{$this->scope}";
    }

    protected function getUserInfo4AccessToken($objQyProvider, $arrAccessToken)
    {
        // a) 当用户为企业成员时
        // CorpId	用户所属企业的corpid
        // UserId	用户在企业内的UserID，如果该企业与第三方应用有授权关系时，返回明文UserId，否则返回密文UserId
        // DeviceId	手机设备号(由企业微信在安装时随机生成，删除重装会改变，升级不受影响)
        // user_ticket	成员票据，最大为512字节。
        // scope为snsapi_userinfo或snsapi_privateinfo，且用户在应用可见范围之内时返回此参数。后续利用该参数可以获取用户信息或敏感信息，参见“第三方使用user_ticket获取成员详情”。
        // expires_in	user_ticket的有效时间（秒），随user_ticket一起返回
        // open_userid	全局唯一。对于同一个服务商，不同应用获取到企业内同一个成员的open_userid是相同的，最多64个字节。仅第三方应用可获取
        if (isset($arrAccessToken['UserId'])) {
            $arrAccessToken['openid'] = '';
            $arrAccessToken['userid'] = $arrAccessToken['UserId'];
            $arrAccessToken['is_qy_member'] = 1;
            $arrAccessToken['qyuserid'] = $arrAccessToken['userid'];
            $arrAccessToken['open_userid'] = $arrAccessToken['open_userid'];

            // // 用户授权的作用域，使用逗号（,）分隔
            // $scopeArr = \explode(',', $arrAccessToken['scope']);
            // if (in_array('snsapi_userinfo', $scopeArr) || in_array('snsapi_privateinfo', $scopeArr)) {
            //     // 先判断用户在数据库中是否存在最近一周产生的openid，如果不存在，则再动用网络请求，进行用户信息获取
            //     $userInfo = $this->modelQyweixinUser->getUserInfoByIdLastWeek($arrAccessToken['userid'], $this->authorizer_appid, $this->provider_appid, $this->now);
            //     if (true || empty($userInfo)) {
            //         $updateInfoFromWx = true;
            //         if (!empty($arrAccessToken['user_ticket'])) {
            //             $userInfo = $objQyProvider->getUserDetail3rd($arrAccessToken['access_token'], $arrAccessToken['user_ticket']);
            //             if (isset($userInfo['errcode'])) {
            //                 throw new \Exception("获取用户信息失败，原因:" . \App\Common\Utils\Helper::myJsonEncode($userInfo));
            //             }
            //         }
            //     }
            // }
        } // b) 非企业成员授权时
        // OpenId	非企业成员的标识，对当前服务商唯一
        // DeviceId	手机设备号(由企业微信在安装时随机生成，删除重装会改变，升级不受影响)
        elseif (isset($arrAccessToken['OpenId'])) {
            $arrAccessToken['userid'] = '';
            $arrAccessToken['openid'] = $arrAccessToken['OpenId'];
            $arrAccessToken['is_qy_member'] = 0;
            $arrAccessToken['qyuserid'] = $arrAccessToken['openid'];
            $arrAccessToken['open_userid'] = "";
        }

        $userInfo = array();
        $userInfo['userid'] = $arrAccessToken['userid'];
        $userInfo['openid'] = $arrAccessToken['openid'];
        $userInfo['name'] = isset($arrAccessToken['name']) ? $arrAccessToken['name'] : "";
        $userInfo['avatar'] = isset($arrAccessToken['avatar']) ? $arrAccessToken['avatar'] : "";
        $userInfo['access_token'] = array_merge($arrAccessToken, $userInfo);

        return array('arrAccessToken' => $arrAccessToken, 'userInfo' => $userInfo);
    }
}
