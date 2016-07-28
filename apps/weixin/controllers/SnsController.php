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

    private $appid;

    private $scope;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        
        $this->_user = new User();
        $this->_app = new Application();
        $this->_tracking = new ScriptTracking();
        $this->_callbackurls = new Callbackurls();
        
        $this->_config = $this->getDI()->get('config');
        $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : $this->_config['weixin']['appid'];
        $this->scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'snsapi_userinfo';
        $this->_appConfig = $this->_app->getApplicationInfoByAppId($this->appid);
        
        $this->_weixin = new \Weixin\Client();
        if (! empty($this->_appConfig['access_token'])) {
            $this->_weixin->setAccessToken($this->_appConfig['access_token']);
        }
    }

    /**
     * http://www.example.com/weixin/sns/index?appid=xxx&redirect=http%3A%2F%2Fwww.baidu.com%2F%3Fa%3Dqw%26b%3D%E4%B8%AD%E5%9B%BD&scope=[snsapi_userinfo(default)|snsapi_base|snsapi_login]&dc=1
     * 引导用户去往登录授权
     */
    public function indexAction()
    {
        $_SESSION['oauth_start_time'] = microtime(true);
        $scheme = $this->getRequest()->getScheme();
        $_SESSION['weixin_sns_url'] = "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        try {
            $redirect = isset($_GET['redirect']) ? trim(trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
            $dc = isset($_GET['dc']) ? intval($_GET['dc']) : 0; // 是否检查回调域名
            
            if ($dc) {
                // 添加重定向域的检查
                $isValid = $this->_callbackurls->isValid($redirect);
                if (empty($isValid)) {
                    die('回调地址不合法');
                }
            }
            
            if (! empty($_SESSION['iWeixin']["accessToken_{$this->appid}_{$this->scope}"])) {
                
                $arrAccessToken = $_SESSION['iWeixin']["accessToken_{$this->appid}_{$this->scope}"];
                
                $redirect = $this->getRedirectUrl($redirect, $arrAccessToken);
                
                // print_r($arrAccessToken);
                // die('session:' . $redirect);
                
                header("location:{$redirect}");
                fastcgi_finish_request();
                $this->_tracking->record("授权session存在", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                exit();
            } elseif (! empty($_COOKIE["__ACCESSTOKEN2_{$this->appid}_{$this->scope}__"])) {
                
                $arrAccessToken = json_decode($_COOKIE["__ACCESSTOKEN2_{$this->appid}_{$this->scope}__"], true);
                
                $redirect = $this->getRedirectUrl($redirect, $arrAccessToken);
                
                // print_r($arrAccessToken);
                // die('cookie:' . $redirect);
                
                header("location:{$redirect}");
                fastcgi_finish_request();
                $this->_tracking->record("授权cookie存在", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                exit();
            } else {
                $moduleName = 'weixin';
                $controllerName = 'sns';
                $actionName = 'callback';
                
                $redirectUri = 'http://';
                $redirectUri .= $_SERVER["HTTP_HOST"];
                $redirectUri .= '/' . $moduleName;
                $redirectUri .= '/' . $controllerName;
                $redirectUri .= '/callback';
                $redirectUri .= '?appid=' . $this->appid;
                $redirectUri .= '&scope=' . $this->scope;
                $redirectUri .= '&redirect=' . urlencode($redirect);
                
                $appid = $this->_appConfig['appid'];
                $secret = $this->_appConfig['secret'];
                $sns = new \Weixin\Token\Sns($appid, $secret);
                $sns->setRedirectUri($redirectUri);
                $sns->setScope($this->scope);
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
                if ($arrAccessToken['scope'] === 'snsapi_userinfo' || $arrAccessToken['scope'] === 'snsapi_login') {
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
                            $arrAccessToken['nickname'] = ($userInfo['nickname']);
                        }
                        
                        if (! empty($userInfo['headimgurl'])) {
                            $arrAccessToken['headimgurl'] = stripslashes($userInfo['headimgurl']);
                        }
                    }
                    $_SESSION['iWeixin']["accessToken_{$this->appid}_{$arrAccessToken['scope']}"] = $arrAccessToken;
                    $path = '/';
                    $expireTime = time() + 1.5 * 3600;
                    setcookie("__ACCESSTOKEN2_{$this->appid}_{$arrAccessToken['scope']}__", json_encode($arrAccessToken), $expireTime, $path);
                    
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
                $this->_tracking->record("SNS授权", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
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

    private function getRedirectUrl($redirect, $arrAccessToken)
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
        return $redirect;
    }
}

