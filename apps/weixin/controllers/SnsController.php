<?php
namespace App\Weixin\Controllers;

use App\Weixin\Models\User;
use App\Weixin\Models\Application;
use App\Weixin\Models\ScriptTracking;
use App\Weixin\Models\Callbackurls;

class SnsController extends ControllerBase
{

    protected $_weixin;

    protected $_user;

    protected $_app;

    protected $_config;

    protected $_tracking;

    protected $_appConfig;

    protected $_callbackurls;

    protected $appid;

    protected $scope;

    protected $state;

    protected $cookie_session_key = 'iWeixin';

    protected $trackingKey = "SNS授权";

    protected $controllerName = 'sns';

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        
        $this->_user = new User();
        $this->_tracking = new ScriptTracking();
        $this->_callbackurls = new Callbackurls();
        
        $this->_config = $this->getDI()->get('config');
        $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : $this->_config['weixin']['appid'];
        $this->scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'snsapi_userinfo';
        $this->state = isset($_GET['state']) ? trim($_GET['state']) : 'wx';
        
        $this->doInitializeLogic();
    }

    /**
     * http://www.example.com/weixin/sns/index?appid=xxx&redirect=http%3A%2F%2Fwww.baidu.com%2F%3Fa%3Dqw%26b%3D%E4%B8%AD%E5%9B%BD&scope=[snsapi_userinfo(default)|snsapi_base|snsapi_login]&dc=1
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
                $isValid = $this->_callbackurls->isValid($redirect);
                if (empty($isValid)) {
                    die('回调地址不合法');
                }
            }
            
            if (! $refresh && ! empty($_SESSION[$this->cookie_session_key]["accessToken_{$this->appid}_{$this->scope}"])) {
                
                $arrAccessToken = $_SESSION[$this->cookie_session_key]["accessToken_{$this->appid}_{$this->scope}"];
                
                $redirect = $this->getRedirectUrl($redirect, $arrAccessToken);
                
                // print_r($arrAccessToken);
                // die('session:' . $redirect);
                
                header("location:{$redirect}");
                fastcgi_finish_request();
                $this->_tracking->record("授权session存在", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                exit();
            } elseif (! $refresh && ! empty($_COOKIE["__{$this->cookie_session_key}_{$this->appid}_{$this->scope}__"])) {
                
                $arrAccessToken = json_decode($_COOKIE["__{$this->cookie_session_key}_{$this->appid}_{$this->scope}__"], true);
                
                $redirect = $this->getRedirectUrl($redirect, $arrAccessToken);
                
                // print_r($arrAccessToken);
                // die('cookie:' . $redirect);
                
                header("location:{$redirect}");
                fastcgi_finish_request();
                $this->_tracking->record("授权cookie存在", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                exit();
            } else {
                $_SESSION['sns_redirect'] = $redirect;
                
                $moduleName = 'weixin';
                $controllerName = $this->controllerName;
                
                $redirectUri = $scheme . '://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/' . $moduleName;
                $redirectUri .= '/' . $controllerName;
                $redirectUri .= '/callback';
                $redirectUri .= '?appid=' . $this->appid;
                // $redirectUri .= '&redirect=' . $redirect;
                
                $this->getAuthorizeUrl($redirectUri);
            }
        } catch (\Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    /**
     * 处理微信的回调数据
     *
     * @return boolean
     */
    public function callbackAction()
    {
        try {
            // $redirect = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
            $redirect = empty($_SESSION['sns_redirect']) ? '' : $_SESSION['sns_redirect'];
            if (empty($redirect)) {
                throw new \Exception("回调地址未定义");
            }
            
            $sourceFromUserName = ! empty($_GET['FromUserName']) ? $_GET['FromUserName'] : null;
            
            $updateInfoFromWx = false;
            
            $microtime_start = microtime(true);
            
            // 获取accesstoken
            $arrAccessToken = $this->getAccessToken();
            
            $t1elapsed = microtime(true) - $microtime_start;
            $arrAccessToken['t1elapsed'] = $t1elapsed;
            
            if (! isset($arrAccessToken['errcode'])) {
                // 授权成功后，记录该微信用户的基本信息
                if ($arrAccessToken['scope'] === 'snsapi_userinfo' || $arrAccessToken['scope'] === 'snsapi_login') {
                    $microtime_start = microtime(true);
                    // 先判断用户在数据库中是否存在最近一周产生的openid，如果不存在，则再动用网络请求，进行用户信息获取
                    $userInfo = $this->_user->getUserInfoByIdLastWeek($arrAccessToken['openid']);
                    if ($userInfo == null) {
                        $updateInfoFromWx = true;
                        $weixin = new \Weixin\Client();
                        $weixin->setSnsAccessToken($arrAccessToken['access_token']);
                        $userInfo = $weixin->getSnsManager()->getSnsUserInfo($arrAccessToken['openid']);
                        if (isset($userInfo['errcode'])) {
                            throw new \Exception("获取用户信息失败，原因:" . json_encode($userInfo, JSON_UNESCAPED_UNICODE));
                        }
                    }
                    $t2elapsed = microtime(true) - $microtime_start;
                    $arrAccessToken['t2elapsed'] = $t2elapsed;
                    $userInfo['access_token'] = $arrAccessToken;
                }
                
                if (isset($arrAccessToken['openid'])) {
                    
                    if (! empty($userInfo)) {
                        if (! empty($userInfo['nickname'])) {
                            $arrAccessToken['nickname'] = ($userInfo['nickname']);
                        }
                        
                        if (! empty($userInfo['headimgurl'])) {
                            $arrAccessToken['headimgurl'] = stripslashes($userInfo['headimgurl']);
                        }
                        
                        if (! empty($userInfo['unionid'])) {
                            $arrAccessToken['unionid'] = ($userInfo['unionid']);
                        }
                    }
                    $_SESSION[$this->cookie_session_key]["accessToken_{$this->appid}_{$arrAccessToken['scope']}"] = $arrAccessToken;
                    $path = '/';
                    $expireTime = time() + 1.5 * 3600;
                    setcookie("__{$this->cookie_session_key}_{$this->appid}_{$arrAccessToken['scope']}__", json_encode($arrAccessToken), $expireTime, $path);
                    
                    $redirect = $this->getRedirectUrl($redirect, $arrAccessToken);
                    
                    if ($sourceFromUserName !== null && $sourceFromUserName == $arrAccessToken['openid']) {
                        $redirect = $this->addUrlParameter($redirect, array(
                            '__self' => true
                        ));
                    }
                }
                
                // die($redirect);
                header("location:{$redirect}");
                // 调整数据库操作的执行顺序，优化跳转速度
                fastcgi_finish_request();
                if ($updateInfoFromWx) {
                    $userInfo['headimgurl'] = stripslashes($userInfo['headimgurl']);
                    $this->_user->updateUserInfoBySns($arrAccessToken['openid'], $userInfo);
                }
                $this->_tracking->record($this->trackingKey, $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                
                $objService = \App\Weixin\Services\Base::getServiceObject();
                $objService->doSnsCallback($arrAccessToken);
                
                exit();
            } else {
                // 如果用户未授权登录，点击取消，自行设定取消的业务逻辑
                throw new \Exception("获取token失败,原因:" . json_encode($arrAccessToken, JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    public function geturlAction()
    {
        // http://www.jizigou.com/weixin/sns/geturl?appid=wxa7f90ea22051777d&scope=snsapi_login&state=wx
        try {
            $redirect = "http://{$_SERVER["HTTP_HOST"]}/member/passport/weixinauthorize";
            
            $moduleName = 'weixin';
            $controllerName = 'sns';
            
            $redirectUri = 'http://';
            $redirectUri .= $_SERVER["HTTP_HOST"];
            $redirectUri .= '/' . $moduleName;
            $redirectUri .= '/' . $controllerName;
            $redirectUri .= '/callback';
            $redirectUri .= '?appid=' . $this->appid;
            $redirectUri .= '&scope=' . $this->scope;
            $redirectUri .= '&redirect=' . $redirect;
            
            $appid = $this->_appConfig['appid'];
            $secret = $this->_appConfig['secret'];
            $sns = new \Weixin\Token\Sns($appid, $secret);
            $sns->setRedirectUri($redirectUri);
            $sns->setScope($this->scope);
            $sns->setState($this->state);
            $url = $sns->getAuthorizeUrl(false);
            die($url);
        } catch (\Exception $e) {
            var_dump($e);
            return false;
        }
    }

    protected function addUrlParameter($url, array $params)
    {
        if (! empty($params)) {
            foreach ($params as $key => $value) {
                // if (strpos($url, $key) === false || ($key == "FromUserName")) {
                if (strpos($url, '?') === false)
                    $url .= "?{$key}=" . $value;
                else
                    $url .= "&{$key}=" . $value;
                // }
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
            'FromUserName' => $arrAccessToken['openid']
        ));
        
        // 计算signkey
        $timestamp = time();
        $signkey = $this->getSignKey($arrAccessToken['openid'], $timestamp);
        $redirect = $this->addUrlParameter($redirect, array(
            'signkey' => $signkey
        ));
        $redirect = $this->addUrlParameter($redirect, array(
            'timestamp' => $timestamp
        ));
        
        if (! empty($arrAccessToken['nickname'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                'nickname' => urlencode($arrAccessToken['nickname'])
            ));
        }
        
        if (! empty($arrAccessToken['headimgurl'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                'headimgurl' => urlencode(stripslashes($arrAccessToken['headimgurl']))
            ));
        }
        
        if (! empty($arrAccessToken['unionid'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                'unionid' => $arrAccessToken['unionid']
            ));
            $signkey = $this->getSignKey($arrAccessToken['unionid'], $timestamp);
            $redirect = $this->addUrlParameter($redirect, array(
                'signkey2' => $signkey
            ));
        }
        
        if (! empty($arrAccessToken['t1elapsed'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                't1elapsed' => $arrAccessToken['t1elapsed']
            ));
        }
        
        if (! empty($arrAccessToken['t2elapsed'])) {
            $redirect = $this->addUrlParameter($redirect, array(
                't2elapsed' => $arrAccessToken['t2elapsed']
            ));
        }
        
        return $redirect;
    }

    /**
     * 初始化
     */
    protected function doInitializeLogic()
    {
        $this->_app = new Application();
        $this->_appConfig = $this->_app->getApplicationInfoByAppId($this->appid);
        
        if (empty($this->_appConfig)) {
            throw new \Exception('appid所对应的记录不存在');
        }
    }

    protected function getAuthorizeUrl($redirectUri)
    {
        $appid = $this->_appConfig['appid'];
        $secret = $this->_appConfig['secret'];
        $sns = new \Weixin\Token\Sns($appid, $secret);
        $sns->setRedirectUri($redirectUri);
        $sns->setScope($this->scope);
        $sns->setState($this->state);
        $sns->getAuthorizeUrl();
    }

    protected function getAccessToken()
    {
        $appid = $this->_appConfig['appid'];
        $secret = $this->_appConfig['secret'];
        $sns = new \Weixin\Token\Sns($appid, $secret);
        $arrAccessToken = $sns->getAccessToken();
        return $arrAccessToken;
    }
}

