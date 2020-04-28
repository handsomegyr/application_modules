<?php

namespace App\Qyweixin\Controllers;

/**
 * 代公众号发起网页授权
 * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318590&token=&lang=
 */
class ComponentsnsController extends ControllerBase
{

    // 活动ID
    protected $activity_id = 3;

    private $modelQyweixinUser;

    private $modelQyweixinProvider;

    private $modelQyweixinAuthorizer;
    /**
     * @var \App\Qyweixin\Models\ScriptTracking
     */
    private $modelQyweixinScriptTracking;

    private $modelQyweixinCallbackurls;

    // lock key
    private $lock_key_prefix = 'qyweixin_component_sns_';

    private $cookie_session_key = 'qyweixin_component_sns_';

    private $sessionKey;

    private $trackingKey = "第三方服务商SNS授权";

    private $provider_appid;

    private $providerConfig;

    private $authorizer_appid;

    private $authorizerConfig;

    private $agentid = 0;

    private $scope;

    private $state;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();

        $this->modelQyweixinUser = new \App\Qyweixin\Models\User\User();
        $this->modelQyweixinProvider = new \App\Qyweixin\Models\Provider\Provider();
        $this->modelQyweixinAuthorizer = new \App\Qyweixin\Models\Authorize\Authorizer();
        $this->modelQyweixinScriptTracking = new \App\Qyweixin\Models\ScriptTracking();
        $this->modelQyweixinCallbackurls = new \App\Qyweixin\Models\Callbackurls();
    }

    /**
     * 引导用户去授权
     * 第一步：请求CODE
     * 请求方法
     * 在确保微信公众账号拥有授权作用域（scope参数）的权限的前提下（一般而言，已微信认证的服务号拥有snsapi_base和snsapi_userinfo），使用微信客户端打开以下链接（严格按照以下格式，包括顺序和大小写，并请将参数替换为实际内容）：
     *
     * https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE&provider_appid=provider_appid#wechat_redirect
     * 若提示“该链接无法访问”，请检查参数是否填写错误，是否拥有scope参数对应的授权作用域权限。
     *
     * 参数说明
     * 参数 是否必须 说明
     * appid 是 公众号的appid
     * redirect_uri 是 重定向地址，需要urlencode，这里填写的应是服务开发方的回调地址
     * response_type 是 填code
     * scope 是 授权作用域，拥有多个作用域用逗号（,）分隔
     * state 否 重定向后会带上state参数，开发者可以填写任意参数值，最多128字节
     * provider_appid 是 服务方的appid，在申请创建公众号服务成功后，可在公众号服务详情页找到
     */
    public function indexAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/componentsns/index?appid=wxb4c2aa76686ea8f4&provider_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_userinfo&refresh=1
        // http://wxcrm.eintone.com/qyweixin/api/componentsns/index?appid=wxb4c2aa76686ea8f4&provider_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_userinfo&refresh=1
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            // 初始化
            $this->doInitializeLogic();

            $redirect = isset($_GET['redirect']) ? (trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
            $dc = isset($_GET['dc']) ? intval($_GET['dc']) : 1; // 是否检查回调域名
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
                $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                header("location:{$redirect}");
                exit();
            } else {
                // 存储跳转地址
                $_SESSION['redirect'] = $redirect;
                $_SESSION['state'] = $this->state;
                $_SESSION['provider_appid'] = $this->provider_appid;

                $moduleName = 'qyweixin';
                $controllerName = $this->controllerName;
                $scheme = $this->getRequest()->getScheme();
                $redirectUri = $scheme . '://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/' . $moduleName;
                $redirectUri .= '/' . $controllerName;
                $redirectUri .= '/callback';

                // $redirectUri .= '?provider_appid=' . $this->provider_appid;
                // $redirectUri .= '?appid=' . $this->authorizer_appid;
                // $redirectUri .= '&state=' . $this->state;
                // $redirectUri .= '&scope=' . $this->scope;

                // 授权处理
                $objComponent = new \Weixin\Token\Component($this->authorizer_appid, $this->provider_appid, $this->providerConfig['access_token']);
                $objComponent->setScope($this->scope);
                $objComponent->setState($this->state);
                $objComponent->setRedirectUri($redirectUri);
                $redirectUri = $objComponent->getAuthorizeUrl(false);
                header("location:{$redirectUri}");
                exit();
            }
        } catch (\Exception $e) {
            $this->modelErrorLog->log($this->activity_id, $e, $this->now);
            return abort(500, $e->getMessage());
        }
    }

    /**
     * 第二步：获取code
     *
     * 用户允许授权后，将会重定向到redirect_uri的网址上，并且带上code, state以及appid
     *
     * redirect_uri?code=CODE&state=STATE&appid=APPID
     * 若用户禁止授权，则重定向后不会带上code参数，仅会带上state参数
     *
     * redirect_uri?state=STATE
     * 第二步：通过code换取access_token
     * 请求方法
     * 获取第一步的code后，请求以下链接获取access_token：
     *
     * https://api.weixin.qq.com/sns/oauth2/provider/access_token?appid=APPID&code=CODE&grant_type=authorization_code&provider_appid=provider_appid&component_access_token=COMPONENT_ACCESS_TOKEN
     * 需要注意的是，由于安全方面的考虑，对访问该链接的客户端有IP白名单的要求。
     */
    public function callbackAction()
    {
        // http://wxcrmdemo.jdytoy.com/qyweixin/api/componentsns/callback?appid=xxx&code=xxx&scope=auth_user&state=xxx
        try {
            $provider_appid = empty($_SESSION['provider_appid']) ? "" : $_SESSION['provider_appid'];
            if (empty($provider_appid)) {
                throw new \Exception("provider_appid未定义");
            }
            $_GET['provider_appid'] = $provider_appid;

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

            $updateInfoFromWx = false;
            $sourceFromUserName = !empty($_GET['FromUserName']) ? $_GET['FromUserName'] : '';

            // 第二步：通过code换取access_token
            $objComponent = new \Weixin\Token\Component($this->authorizer_appid, $this->provider_appid, $this->providerConfig['access_token']);
            $arrAccessToken = $objComponent->getAccessToken();
            if (isset($arrAccessToken['errcode'])) {
                throw new \Exception("获取token失败,原因:" . json_encode($arrAccessToken, JSON_UNESCAPED_UNICODE));
            }

            // 授权成功后，记录该微信用户的基本信息

            // 用户授权的作用域，使用逗号（,）分隔
            $scopeArr = \explode(',', $arrAccessToken['scope']);
            if (in_array('snsapi_userinfo', $scopeArr) || in_array('snsapi_login', $scopeArr)) {
                // 先判断用户在数据库中是否存在最近一周产生的openid，如果不存在，则再动用网络请求，进行用户信息获取
                $userInfo = $this->modelQyweixinUser->getUserInfoByIdLastWeek($arrAccessToken['openid'], $this->authorizer_appid, $this->provider_appid, $this->now);
                if (true || empty($userInfo)) {
                    $updateInfoFromWx = true;
                    $weixin = new \Weixin\Client();
                    $weixin->setSnsAccessToken($arrAccessToken['access_token']);
                    $userInfo = $weixin->getSnsManager()->getSnsUserInfo($arrAccessToken['openid']);
                    if (isset($userInfo['errcode'])) {
                        throw new \Exception("获取用户信息失败，原因:" . json_encode($userInfo, JSON_UNESCAPED_UNICODE));
                    }
                }
                $userInfo['access_token'] = array_merge($arrAccessToken, $userInfo);
            }

            if (!empty($userInfo)) {
                if (!empty($userInfo['nickname'])) {
                    $arrAccessToken['nickname'] = ($userInfo['nickname']);
                }

                if (!empty($userInfo['headimgurl'])) {
                    $arrAccessToken['headimgurl'] = stripslashes($userInfo['headimgurl']);
                }

                if (!empty($userInfo['unionid'])) {
                    $arrAccessToken['unionid'] = ($userInfo['unionid']);
                }
            }

            $_SESSION[$this->sessionKey] = $arrAccessToken;

            $redirect = $this->getRedirectUrl4Sns($redirect, $arrAccessToken);

            if ($sourceFromUserName !== null && $sourceFromUserName == $arrAccessToken['openid']) {
                $redirect = $this->addUrlParameter($redirect, array(
                    '__self' => true
                ));
            }

            // 调整数据库操作的执行顺序，优化跳转速度
            if ($updateInfoFromWx) {
                if (!empty($userInfo['headimgurl'])) {
                    $userInfo['headimgurl'] = stripslashes($userInfo['headimgurl']);
                }
                $lock = new \iLock($this->lock_key_prefix . $arrAccessToken['openid'] . $this->authorizer_appid . $this->provider_appid);
                if (!$lock->lock()) {
                    $this->modelQyweixinUser->updateUserInfoBySns($arrAccessToken['openid'], $this->authorizer_appid, $this->provider_appid, $userInfo);
                }
            }
            $this->modelQyweixinScriptTracking->record($this->provider_appid, $this->authorizer_appid, $this->agentid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);

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
        return $this->modelQyweixinAuthorizer->getSignKey($openid, $this->authorizerConfig['secretKey'], $timestamp);
    }

    protected function getRedirectUrl4Sns($redirect, $arrAccessToken)
    {
        $redirect = $this->addUrlParameter($redirect, array(
            'it_userToken' => urlencode($arrAccessToken['access_token'])
        ));

        $redirect = $this->addUrlParameter($redirect, array(
            'it_refreshToken' => urlencode($arrAccessToken['refresh_token'])
        ));

        $redirect = $this->addUrlParameter($redirect, array(
            'it_FromUserName' => $arrAccessToken['openid']
        ));

        // 计算signkey
        $timestamp = time();
        $signkey = $this->getSignKey($arrAccessToken['openid'], $timestamp);
        $redirect = $this->addUrlParameter($redirect, array(
            'it_signkey' => $signkey
        ));
        $redirect = $this->addUrlParameter($redirect, array(
            'it_timestamp' => $timestamp
        ));

        if (!empty($arrAccessToken['nickname'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                'it_nickname' => urlencode($arrAccessToken['nickname'])
            ));
        }

        if (!empty($arrAccessToken['headimgurl'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                'it_headimgurl' => urlencode(stripslashes($arrAccessToken['headimgurl']))
            ));
        }

        if (!empty($arrAccessToken['unionid'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                'it_unionid' => $arrAccessToken['unionid']
            ));
            $signkey = $this->getSignKey($arrAccessToken['unionid'], $timestamp);
            $redirect = $this->addUrlParameter($redirect, array(
                'it_signkey2' => $signkey
            ));
        }

        return $redirect;
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        // 第三方服务商运用ID
        $this->provider_appid = isset($_GET['provider_appid']) ? trim($_GET['provider_appid']) : "";
        if (empty($this->provider_appid)) {
            throw new \Exception("provider_appid为空");
        }
        $this->providerConfig = $this->modelQyweixinProvider->getInfoByAppid($this->provider_appid);
        if (empty($this->providerConfig)) {
            throw new \Exception("provider_appid:{$this->provider_appid}所对应的记录不存在");
        }

        // 授权方ID
        $this->authorizer_appid = isset($_GET['appid']) ? trim($_GET['appid']) : "";
        if (empty($this->authorizer_appid)) {
            throw new \Exception("appid为空");
        }
        $this->authorizerConfig = $this->modelQyweixinAuthorizer->getInfoByAppid($this->provider_appid, $this->authorizer_appid);
        if (empty($this->authorizerConfig)) {
            throw new \Exception("provider_appid:{$this->provider_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
        }

        $this->state = isset($_GET['state']) ? trim($_GET['state']) : uniqid();
        $this->scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'snsapi_userinfo';
        $this->sessionKey = $this->cookie_session_key . "_accessToken_{$this->provider_appid}_{$this->authorizer_appid}_{$this->scope}";
    }
}
