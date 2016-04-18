<?php
namespace App\Weixin\Controllers;

use App\Weixin\Models\User;
use App\Weixin\Models\Application;
use App\Weixin\Models\ScriptTracking;
use App\Weixin\Models\Callbackurls;

class SnsController extends ControllerBase
{

    private $_weixin;

    private $_user;

    private $_app;

    private $_config;

    private $_tracking;

    private $_appConfig;

    private $_callbackurls;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        
        $this->_user = new User();
        $this->_app = new Application();
        $this->_tracking = new ScriptTracking();
        $this->_callbackurls = new Callbackurls();
        $this->_appConfig = $this->_app->getToken();
        $this->_weixin = new \Weixin\Client();
        if (! empty($this->_appConfig['access_token'])) {
            $this->_weixin->setAccessToken($this->_appConfig['access_token']);
        }
    }

    /**
     * http://www.example.com/weixin/sns/index?redirect=回调地址&scope=[snsapi_userinfo(default)|snsapi_base|snsapi_login]&dc=1
     * 引导用户去往登录授权
     */
    public function indexAction()
    {
        $_SESSION['oauth_start_time'] = microtime(true);
        $scheme = $this->getRequest()->getScheme();
        $_SESSION['weixin_sns_url'] = "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        try {
            $scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'snsapi_userinfo';
            $redirect = isset($_GET['redirect']) ? trim($_GET['redirect']) : ''; // 附加参数存储跳转地址
            $dc = isset($_GET['dc']) ? intval($_GET['dc']) : 0; // 是否检查回调域名
            
            if ($dc) {
                // 添加重定向域的检查
                $isValid = $this->_callbackurls->isValid($redirect);
                if (empty($isValid)) {
                    die('回调地址不合法');
                }
            }
            
            if (isset($_SESSION['iWeixin']['accessToken'])) {
                
                $arrAccessToken = $_SESSION['iWeixin']['accessToken'];
                
                if (isset($arrAccessToken['openid'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'FromUserName' => $arrAccessToken['openid']
                    ));
                }
                
                // 计算signkey
                $timestamp = time();
                $signkey = $this->getSignKey($arrAccessToken['openid'], $timestamp);
                $redirect = $this->addUrlParameter($redirect, array(
                    'signkey' => $signkey
                ));
                $redirect = $this->addUrlParameter($redirect, array(
                    'timestamp' => $timestamp
                ));
                
                if (isset($arrAccessToken['nickname'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'nickname' => $arrAccessToken['nickname']
                    ));
                }
                
                if (isset($arrAccessToken['headimgurl'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'headimgurl' => $arrAccessToken['headimgurl']
                    ));
                }
                header("location:{$redirect}");
                fastcgi_finish_request();
                $this->_tracking->record("授权session存在", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                exit();
            } elseif (! empty($_COOKIE['__OPENID__'])) {
                if (isset($_COOKIE['__OPENID__'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'FromUserName' => $_COOKIE['__OPENID__']
                    ));
                }
                
                // 计算signkey
                $timestamp = time();
                $signkey = $this->getSignKey($_COOKIE['__OPENID__'], $timestamp);
                $redirect = $this->addUrlParameter($redirect, array(
                    'signkey' => $signkey
                ));
                $redirect = $this->addUrlParameter($redirect, array(
                    'timestamp' => $timestamp
                ));
                
                if (isset($_COOKIE['nickname'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'nickname' => $_COOKIE['nickname']
                    ));
                }
                
                if (isset($_COOKIE['headimgurl'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'headimgurl' => $_COOKIE['headimgurl']
                    ));
                }
                
                header("location:{$redirect}");
                fastcgi_finish_request();
                $this->_tracking->record("授权cookie存在", $_SESSION['oauth_start_time'], microtime(true), $_COOKIE['__OPENID__']);
                exit();
            } else {
                
                $moduleName = $this->getRequest()->getModuleName();
                $controllerName = $this->getRequest()->getControllerName();
                $actionName = $this->getRequest()->getActionName();
                
                $redirectUri = 'http://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/' . $moduleName;
                $redirectUri .= '/' . $controllerName;
                $redirectUri .= '/callback';
                $redirectUri .= '?redirect=' . urlencode($redirect);
                
                $appid = $this->_appConfig['appid'];
                $secret = $this->_appConfig['secret'];
                $sns = new \Weixin\Token\Sns($appid, $secret);
                $sns->setRedirectUri($redirectUri);
                $sns->setScope($scope);
                $sns->getAuthorizeUrl();
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
            $redirect = isset($_GET['redirect']) ? trim($_GET['redirect']) : '';
            if (empty($redirect)) {
                throw new \Exception("回调地址未定义");
            }
            
            $sourceFromUserName = ! empty($_GET['FromUserName']) ? $_GET['FromUserName'] : null;
            
            $updateInfoFromWx = false;
            $appid = $this->_appConfig['appid'];
            $secret = $this->_appConfig['secret'];
            $sns = new \Weixin\Token\Sns($appid, $secret);
            $arrAccessToken = $sns->getAccessToken();
            
            if (! isset($arrAccessToken['errcode'])) {
                // 授权成功后，记录该微信用户的基本信息
                if ($arrAccessToken['scope'] === 'snsapi_userinfo') {
                    $this->_weixin->setSnsAccessToken($arrAccessToken['access_token']);
                    
                    // 先判断用户在数据库中是否存在最近一周产生的openid，如果不存在，则再动用网络请求，进行用户信息获取
                    $userInfo = $this->_user->getUserInfoByIdLastWeek($arrAccessToken['openid']);
                    if ($userInfo == null) {
                        $updateInfoFromWx = true;
                        $userInfo = $this->_weixin->getSnsManager()->getSnsUserInfo($arrAccessToken['openid']);
                        if (isset($userInfo['errcode'])) {
                            throw new \Exception("获取用户信息失败，原因:" . json_encode($userInfo, JSON_UNESCAPED_UNICODE));
                        }
                    }
                    $userInfo['access_token'] = $arrAccessToken;
                }
                
                if (isset($arrAccessToken['openid'])) {
                    
                    if (! empty($userInfo)) {
                        if (! empty($userInfo['nickname'])) {
                            $arrAccessToken['nickname'] = urlencode($userInfo['nickname']);
                        }
                        
                        if (! empty($userInfo['headimgurl'])) {
                            $arrAccessToken['headimgurl'] = urlencode($userInfo['headimgurl']);
                        }
                    }
                    $_SESSION['iWeixin']['accessToken'] = $arrAccessToken;
                    
                    $path = $this->_config['global']['path'];
                    setcookie('__OPENID__', $arrAccessToken['openid'], time() + 30 * 24 * 3600, $path);
                    if (! empty($arrAccessToken['nickname'])) {
                        setcookie('nickname', $arrAccessToken['nickname'], time() + 30 * 24 * 3600, $path);
                    }
                    if (! empty($arrAccessToken['headimgurl'])) {
                        setcookie('headimgurl', $arrAccessToken['headimgurl'], time() + 30 * 24 * 3600, $path);
                    }
                    
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
                            'nickname' => $arrAccessToken['nickname']
                        ));
                    }
                    
                    if (! empty($arrAccessToken['headimgurl'])) {
                        $redirect = $this->addUrlParameter($redirect, array(
                            'headimgurl' => $arrAccessToken['headimgurl']
                        ));
                    }
                    
                    if ($sourceFromUserName !== null && $sourceFromUserName == $arrAccessToken['openid']) {
                        $redirect = $this->addUrlParameter($redirect, array(
                            '__self' => true
                        ));
                    }
                }
                
                header("location:{$redirect}");
                // 调整数据库操作的执行顺序，优化跳转速度
                fastcgi_finish_request();
                if ($updateInfoFromWx) {
                    $this->_user->updateUserInfoBySns($arrAccessToken['openid'], $userInfo);
                }
                $this->_tracking->record("SNS授权", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
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

    private function addUrlParameter($url, array $params)
    {
        if (! empty($params)) {
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

    private function getSignKey($openid, $timestamp = 0)
    {
        return $this->_app->getSignKey($openid, $this->_appConfig['secretKey'], $timestamp);
    }
}

