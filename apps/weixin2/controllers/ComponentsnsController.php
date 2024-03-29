<?php

namespace App\Weixin2\Controllers;

/**
 * 代公众号发起网页授权
 * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318590&token=&lang=
 */
class ComponentsnsController extends ControllerBase
{

    // 活动ID
    protected $activity_id = 1;
    /**
     * @var \App\Weixin2\Models\User\User
     */
    private $modelWeixinopenUser;
    /**
     * @var \App\Weixin2\Models\Component\Component
     */
    private $modelWeixinopenComponent;
    /**
     * @var \App\Weixin2\Models\Authorize\Authorizer
     */
    private $modelWeixinopenAuthorizer;
    /**
     * @var \App\Weixin2\Models\ScriptTracking
     */
    private $modelWeixinopenScriptTracking;
    /**
     * @var \App\Weixin2\Models\Callbackurls
     */
    private $modelWeixinopenCallbackurls;

    // lock key
    private $lock_key_prefix = 'weixinopen_component_sns_';

    private $cookie_session_key = 'weixinopen_component_sns_';

    private $sessionKey;

    private $trackingKey = "第三方平台SNS授权";

    private $appid;

    private $appConfig;

    private $component_appid;

    private $componentConfig;

    private $authorizer_appid;

    private $authorizerConfig;

    private $scope;

    private $state;

    //应用类型 1:公众号 2:小程序 3:订阅号
    private $app_type = 0;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();

        $this->modelWeixinopenUser = new \App\Weixin2\Models\User\User();
        $this->modelWeixinopenComponent = new \App\Weixin2\Models\Component\Component();
        $this->modelWeixinopenAuthorizer = new \App\Weixin2\Models\Authorize\Authorizer();
        // $this->modelWeixinopenScriptTracking = new \App\Weixin2\Models\ScriptTracking();
        $this->modelWeixinopenCallbackurls = new \App\Weixin2\Models\Callbackurls();
    }

    /**
     * 引导用户去授权
     * 第一步：请求CODE
     * 请求方法
     * 在确保微信公众账号拥有授权作用域（scope参数）的权限的前提下（一般而言，已微信认证的服务号拥有snsapi_base和snsapi_userinfo），使用微信客户端打开以下链接（严格按照以下格式，包括顺序和大小写，并请将参数替换为实际内容）：
     *
     * https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE&component_appid=component_appid#wechat_redirect
     * 若提示“该链接无法访问”，请检查参数是否填写错误，是否拥有scope参数对应的授权作用域权限。
     *
     * 参数说明
     * 参数 是否必须 说明
     * appid 是 公众号的appid
     * redirect_uri 是 重定向地址，需要urlencode，这里填写的应是服务开发方的回调地址
     * response_type 是 填code
     * scope 是 授权作用域，拥有多个作用域用逗号（,）分隔
     * state 否 重定向后会带上state参数，开发者可以填写任意参数值，最多128字节
     * component_appid 是 服务方的appid，在申请创建公众号服务成功后，可在公众号服务详情页找到
     */
    public function authorizeAction()
    {
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/componentsns/authorize?appid=wxb4c2aa76686ea8f4&component_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_userinfo&refresh=1
        // http://www.myapplicationmodule.com/weixinopen/api/componentsns/authorize?appid=wxb4c2aa76686ea8f4&component_appid=wxca8519f703c07d32&redirect=https%3A%2F%2Fwww.baidu.com%2F&state=qwerty&scope=snsapi_userinfo&refresh=1
        $_SESSION['oauth_start_time'] = microtime(true);
        try {
            // 初始化
            $this->doInitializeLogic();

            $redirect = isset($_GET['redirect']) ? (trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
            $dc = isset($_GET['dc']) ? intval($_GET['dc']) : 1; // 是否检查回调域名
            $refresh = isset($_GET['refresh']) ? intval($_GET['refresh']) : 0; // 是否刷新

            if ($dc) {
                // 添加重定向域的检查
                $isValid = $this->modelWeixinopenCallbackurls->isValid($this->authorizer_appid, $this->component_appid, $redirect);
                if (empty($isValid)) {
                    throw new \Exception("回调地址不合法");
                }
            }

            if (!$refresh && !empty($_SESSION[$this->sessionKey])) {
                $arrAccessToken = $_SESSION[$this->sessionKey];
                $redirect = $this->getRedirectUrl4Sns($redirect, $arrAccessToken);
                // $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                header("location:{$redirect}");
                exit();
            } else {
                // 存储跳转地址
                $_SESSION['redirect'] = $redirect;
                $_SESSION['state'] = $this->state;
                $_SESSION['appid'] = $this->appid;

                $moduleName = 'weixin2';
                $controllerName = $this->controllerName;
                $scheme = $this->getRequest()->getScheme();
                $redirectUri = $scheme . '://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/' . $moduleName;
                $redirectUri .= '/' . $controllerName;
                $redirectUri .= '/callback';

                // 授权处理
                $objComponent = new \Weixin\Token\Component($this->authorizer_appid, $this->component_appid, $this->componentConfig['access_token']);
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
     * https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=APPID&code=CODE&grant_type=authorization_code&component_appid=COMPONENT_APPID&component_access_token=COMPONENT_ACCESS_TOKEN
     * 需要注意的是，由于安全方面的考虑，对访问该链接的客户端有IP白名单的要求。
     */
    public function callbackAction()
    {
        // http://wxcrmdemo.jdytoy.com/weixinopen/api/componentsns/callback?appid=xxx&code=xxx&scope=auth_user&state=xxx
        try {
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

            $updateInfoFromWx = false;
            $sourceFromUserName = !empty($_GET['FromUserName']) ? $_GET['FromUserName'] : '';

            // 第二步：通过code换取access_token
            $objComponent = new \Weixin\Token\Component($this->authorizer_appid, $this->component_appid, $this->componentConfig['access_token']);
            $arrAccessToken = $objComponent->getAccessToken();
            if (isset($arrAccessToken['errcode'])) {
                throw new \Exception("获取token失败,原因:" . \App\Common\Utils\Helper::myJsonEncode($arrAccessToken));
            }

            // 授权成功后，记录该微信用户的基本信息
            $updateInfoFromWx = true;
            $userInfo = array();
            // 用户授权的作用域，使用逗号（,）分隔
            $scopeArr = \explode(',', $arrAccessToken['scope']);
            if (in_array('snsapi_userinfo', $scopeArr) || in_array('snsapi_login', $scopeArr)) {
                // 先判断用户在数据库中是否存在最近一周产生的openid，如果不存在，则再动用网络请求，进行用户信息获取
                $userInfo = $this->modelWeixinopenUser->getUserInfoByIdLastWeek($arrAccessToken['openid'], $this->authorizer_appid, $this->component_appid, $this->now);
                if (true || empty($userInfo)) {
                    $updateInfoFromWx = true;
                    $weixin = new \Weixin\Client();
                    $weixin->setSnsAccessToken($arrAccessToken['access_token']);
                    $userInfo = $weixin->getSnsManager()->getSnsUserInfo($arrAccessToken['openid']);
                    if (isset($userInfo['errcode'])) {
                        throw new \Exception("获取用户信息失败，原因:" . \App\Common\Utils\Helper::myJsonEncode($userInfo));
                    }
                }
            }
            $userInfo['access_token'] = array_merge($arrAccessToken, $userInfo);
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
                $lock = new \iLock($this->lock_key_prefix . $arrAccessToken['openid'] . $this->authorizer_appid . $this->component_appid);
                if (!$lock->lock()) {
                    $this->modelWeixinopenUser->updateUserInfoBySns($arrAccessToken['openid'], $this->authorizer_appid, $this->component_appid, $userInfo);
                }
            }
            // $this->modelWeixinopenScriptTracking->record($this->component_appid, $this->authorizer_appid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);

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
        return $this->modelWeixinopenAuthorizer->getSignKey($openid, $this->authorizerConfig['secretKey'], $timestamp);
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
        // 应用ID
        $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : "";
        if (empty($this->appid)) {
            throw new \Exception("appid为空");
        }
        $this->appConfig = $this->modelWeixinopenSnsApplication->getInfoByAppid($this->appid);
        if (empty($this->appConfig)) {
            throw new \Exception("appid:{$this->appid}所对应的记录不存在");
        }

        $isValid = $this->modelWeixinopenSnsApplication->checkIsValid($this->appConfig, $this->now);
        if (empty($isValid)) {
            throw new \Exception("appid:{$this->appid}所对应的记录已无效");
        }
        // 第三方平台运用ID
        $this->component_appid = $this->appConfig['component_appid'];
        if (empty($this->component_appid)) {
            throw new \Exception("component_appid为空");
        }

        // 授权方ID
        $this->authorizer_appid = $this->appConfig['authorizer_appid'];
        if (empty($this->authorizer_appid)) {
            throw new \Exception("authorizer_appid为空");
        }
        $this->authorizerConfig = $this->modelWeixinopenAuthorizer->getInfoByAppid($this->component_appid, $this->authorizer_appid);
        if (empty($this->authorizerConfig)) {
            throw new \Exception("component_appid:{$this->component_appid}和authorizer_appid:{$this->authorizer_appid}所对应的记录不存在");
        }
        //应用类型 1:公众号 2:小程序 3:订阅号
        $this->app_type = intval($this->authorizerConfig['app_type']);

        $this->state = isset($_GET['state']) ? trim($_GET['state']) : uniqid();
        $this->scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'snsapi_userinfo';
        $this->sessionKey = $this->cookie_session_key . "_accessToken_{$this->component_appid}_{$this->authorizer_appid}_{$this->scope}";
    }
}
