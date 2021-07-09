<?php

namespace App\Alipay\Controllers;

use App\Alipay\Models\User;
use App\Alipay\Models\Application;
use App\Alipay\Models\ScriptTracking;
use App\Alipay\Models\Callbackurls;

class SnsController extends ControllerBase
{

    protected $_user;

    protected $_app;

    protected $_tracking;

    protected $_callbackurls;

    protected $_config;

    protected $_appConfig;

    protected $appid;

    protected $scope;

    protected $state;

    protected $cookie_session_key = 'iAlipay';

    protected $trackingKey = "SNS授权";

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();

        $this->_config = $this->getDI()->get('config');
        $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : $this->_config['alipay']['appid'];
        $this->scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'auth_user';
        $this->state = isset($_GET['state']) ? trim($_GET['state']) : '';

        $this->_app = new Application();
        $this->_user = new User();
        $this->_tracking = new ScriptTracking();
        $this->_callbackurls = new Callbackurls();

        $this->doInitializeLogic();
    }

    /**
     * scope接口权限值，目前只支持auth_user和auth_base两个值
     * http://www.example.com/alipay/sns/index?appid=xxx&redirect=回调地址&scope=[auth_user(default)|auth_base]&dc=1&state=xxx
     * http://www.myapplicationmodule.com/alipay/sns/index?appid=2017071707783020&redirect=https%3A%2F%2Fwww.baidu.com%2F&scope=auth_user&state=xxx&refresh=1
     * 引导用户去往登录授权
     */
    public function indexAction()
    {
        $_SESSION['oauth_start_time'] = microtime(true);
        $scheme = $this->getRequest()->getScheme();
        $_SESSION["{$this->cookie_session_key}_sns_url"] = "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        try {
            $redirect = isset($_GET['redirect']) ? trim(trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
            $dc = isset($_GET['dc']) ? intval($_GET['dc']) : 0; // 是否检查回调域名
            $refresh = isset($_GET['refresh']) ? intval($_GET['refresh']) : 0; // 是否刷新

            if ($dc) {
                // 添加重定向域的检查
                $isValid = $this->_callbackurls->isValid($this->appid, $redirect);
                if (empty($isValid)) {
                    die('回调地址不合法');
                }
            }

            if (!$refresh && !empty($_SESSION[$this->cookie_session_key]["accessToken_{$this->appid}_{$this->scope}"])) {

                $arrAccessToken = $_SESSION[$this->cookie_session_key]["accessToken_{$this->appid}_{$this->scope}"];

                $redirect = $this->getRedirectUrl($redirect, $arrAccessToken);

                // print_r($arrAccessToken);
                // die('session:' . $redirect);

                header("location:{$redirect}");
                fastcgi_finish_request();
                $this->_tracking->record($this->appid, "授权session存在", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['user_id']);
                exit();
            } elseif (!$refresh && !empty($_COOKIE["__{$this->cookie_session_key}_{$this->appid}_{$this->scope}__"])) {

                $arrAccessToken = json_decode($_COOKIE["__{$this->cookie_session_key}_{$this->appid}_{$this->scope}__"], true);

                $redirect = $this->getRedirectUrl($redirect, $arrAccessToken);

                // print_r($arrAccessToken);
                // die('cookie:' . $redirect);

                header("location:{$redirect}");
                fastcgi_finish_request();
                $this->_tracking->record($this->appid, "授权cookie存在", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['user_id']);
                exit();
            } else {
                $redirectUri = 'http://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/alipay';
                $redirectUri .= '/sns';
                $redirectUri .= '/callback';
                $redirectUri .= '?appid=' . $this->appid;
                $redirectUri .= '&scope=' . $this->scope;
                $redirectUri .= '&redirect=' . $redirect;

                // 授权处理
                $redirectUri = \iAlipay::getAuthorizeUrl($redirectUri, $this->appid, $this->scope, $this->state);
                header("location:{$redirectUri}");
                exit();
            }
        } catch (\Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    /**
     * 第二步：获取auth_code
     *
     * 当用户授权成功后，会跳转至开发者定义的回调页面，支付宝会在回调页面请求中加入参数，包括auth_code、app_id、scope等，需要注意的是支付宝仅保证auth_code、app_id以及scope参数的有效性。
     * 支付宝请求开发者回调页面示例如下：
     *
     * http://example.com/doc/toAuthPage.html?app_id=2014101500013658&source=alipay_wallet&scope=auth_user&auth_code=ca34ea491e7146cc87d25fca24c4cD11
     */
    public function callbackAction()
    {
        try {
            $app_id = isset($_GET['app_id']) ? ($_GET['app_id']) : '';
            $userOutputs = isset($_GET['userOutputs']) ? ($_GET['userOutputs']) : '';
            $redirect = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : '';
            $auth_code = isset($_GET['auth_code']) ? ($_GET['auth_code']) : '';

            $source = isset($_GET['source']) ? ($_GET['source']) : '';
            $alipay_token = isset($_GET['alipay_token']) ? ($_GET['alipay_token']) : '';
            $readauth = isset($_GET['readauth']) ? ($_GET['readauth']) : '';
            if (empty($redirect)) {
                throw new \Exception("回调地址未定义");
            }

            $sourceUserId = !empty($_GET['sourceUserId']) ? $_GET['sourceUserId'] : '';
            $updateInfoFromWx = false;
            if (!empty($auth_code)) {
                // 第三步：使用auth_code换取接口access_token及用户userId
                // 接口名称：alipay.system.oauth.token
                // 换取授权访问令牌，开发者可通过获取到的auth_code换取access_token和用户userId。auth_code作为换取access_token的票据，每次用户授权完成，回调地址中的auth_code将不一样，auth_code只能使用一次，一天未被使用自动过期。
                $objiAlipay = new \iAlipay($this->_appConfig['app_id'], $this->_appConfig['merchant_private_key'], $this->_appConfig['merchant_public_key'], $this->_appConfig['alipay_public_key'], $this->_appConfig['charset'], $this->_appConfig['gatewayUrl'], $this->_appConfig['sign_type']);
                $arrAccessToken = $objiAlipay->alipaySystemOauthTokenRequest($auth_code);

                // 只有在这个授权方式下获取用户信息
                $userInfo = array();
                if ($this->scope == 'auth_user') {
                    // 先判断用户在数据库中是否存在最近一周产生的openid，如果不存在，则再动用网络请求，进行用户信息获取
                    $userInfo = $this->_user->getUserInfoByIdLastWeek($arrAccessToken['user_id']);
                    // $userInfo = $this->_user->getUserInfoByUserId($arrAccessToken['user_id']);
                    if (empty($userInfo)) {
                        $updateInfoFromWx = true;
                        $userInfo = $objiAlipay->alipayUserInfoRequest($arrAccessToken['access_token']);
                        $userInfo['access_token'] = $arrAccessToken;
                        $userInfo['app_id'] = $this->appid;
                    }
                }

                if (isset($arrAccessToken['user_id'])) {

                    if (!empty($userInfo)) {
                        if (!empty($userInfo['nick_name'])) {
                            $arrAccessToken['nickname'] = ($userInfo['nick_name']);
                        }

                        if (!empty($userInfo['avatar'])) {
                            $arrAccessToken['headimgurl'] = stripslashes($userInfo['avatar']);
                        }
                    }
                    $_SESSION[$this->cookie_session_key]["accessToken_{$this->appid}_{$this->scope}"] = $arrAccessToken;
                    $path = $this->_config['global']['path'];
                    $expireTime = time() + 1.5 * 3600;
                    setcookie("__{$this->cookie_session_key}_{$this->appid}_{$this->scope}__", json_encode($arrAccessToken), $expireTime, $path);

                    $redirect = $this->getRedirectUrl($redirect, $arrAccessToken);

                    if ($sourceUserId !== null && $sourceUserId == $arrAccessToken['user_id']) {
                        $redirect = $this->addUrlParameter($redirect, array(
                            '__self' => true
                        ));
                    }
                }

                header("location:{$redirect}");
                // 调整数据库操作的执行顺序，优化跳转速度
                fastcgi_finish_request();
                if ($updateInfoFromWx) {
                    $userInfo['headimgurl'] = stripslashes($userInfo['headimgurl']);
                    $this->_user->updateUserInfoBySns($arrAccessToken['user_id'], $userInfo);
                }
                $this->_tracking->record($this->appid, $this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['user_id']);

                exit();
            } else {
                // 循环授权
                // header("location:{$_SESSION['weixin_sns_url']}");
                // exit();
                // 如果用户未授权登录，点击取消，自行设定取消的业务逻辑
                throw new \Exception("获取token失败,原因:" . json_encode($arrAccessToken, JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    protected function addUrlParameter($url, array $params)
    {
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (strpos($url, $key) === false || ($key == "user_id")) {
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
        return $this->_app->getSignKey($openid, $this->_appConfig['secretKey'], $timestamp);
    }

    protected function getRedirectUrl($redirect, $arrAccessToken)
    {
        $redirect = $this->addUrlParameter($redirect, array(
            'userToken' => urlencode($arrAccessToken['access_token'])
        ));

        $redirect = $this->addUrlParameter($redirect, array(
            'refreshToken' => urlencode($arrAccessToken['refresh_token'])
        ));

        $redirect = $this->addUrlParameter($redirect, array(
            'user_id' => $arrAccessToken['user_id']
        ));

        // 计算signkey
        $timestamp = time();
        $signkey = $this->getSignKey($arrAccessToken['user_id'], $timestamp);
        $redirect = $this->addUrlParameter($redirect, array(
            'signkey' => $signkey
        ));
        $redirect = $this->addUrlParameter($redirect, array(
            'timestamp' => $timestamp
        ));

        if (!empty($arrAccessToken['nickname'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                'nickname' => urlencode($arrAccessToken['nickname'])
            ));
        }

        if (!empty($arrAccessToken['headimgurl'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                'headimgurl' => urlencode(stripslashes($arrAccessToken['headimgurl']))
            ));
        }
        return $redirect;
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        $this->_appConfig = $this->_app->getApplicationInfoByAppId($this->appid);

        if (empty($this->_appConfig)) {
            throw new \Exception('appid所对应的记录不存在');
        }
    }
}
