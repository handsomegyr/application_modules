<?php
namespace App\Tencent\Controllers;

use Eva\EvaOAuth\Service;
use App\Tencent\Models\User;
use App\Tencent\Models\Application;

class SnsController extends ControllerBase
{

    private $_user;

    private $_app;

    private $appid;

    private $scope;

    private $_config;

    private $_appConfig;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        
        $this->_user = new User();
        $this->_app = new Application();
        
        $this->_config = $this->getDI()->get('config');
        $this->appid = isset($_GET['appid']) ? trim($_GET['appid']) : $this->_config['weixin']['appid'];
        $this->scope = isset($_GET['scope']) ? trim($_GET['scope']) : 'get_user_info';
        
        // 获取设置
        $this->_appConfig = $this->_app->getInfoByAppId($this->appid);
        if (empty($this->_appConfig)) {
            exit("appid不正确");
        }
    }

    /**
     * http://www.jizigou.com/tencent/sns/index?appid=563b20c5bbcb2605038b4568&redirect=http%3A%2F%2Fwwww.baidu.com%2F&state=1234
     * 引导用户去往登录授权
     */
    public function indexAction()
    {
        $_SESSION['oauth_start_time'] = microtime(true);
        $scheme = $this->getRequest()->getScheme();
        $_SESSION['tencent_sns_url'] = "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        try {
            $redirect = isset($_GET['redirect']) ? trim(trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
            
            if (! empty($_SESSION['iTencent']["accessToken_{$this->appid}_{$this->scope}"])) {
                
                $arrAccessToken = $_SESSION['iTencent']["accessToken_{$this->appid}_{$this->scope}"];
                
                $redirect = $this->getRedirectUrl($redirect, $arrAccessToken);
                
                // print_r($arrAccessToken);
                // die('session:' . $redirect);
                
                header("location:{$redirect}");
                fastcgi_finish_request();
                
                exit();
            } elseif (! empty($_COOKIE["__iTencent_accessToken_{$this->appid}_{$this->scope}__"])) {
                
                $arrAccessToken = json_decode($_COOKIE["__iTencent_accessToken_{$this->appid}_{$this->scope}__"], true);
                
                $redirect = $this->getRedirectUrl($redirect, $arrAccessToken);
                
                // print_r($arrAccessToken);
                // die('cookie:' . $redirect);
                
                header("location:{$redirect}");
                fastcgi_finish_request();
                exit();
            } else {
                $moduleName = 'tencent';
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
                
                // 初始化QQ适配器
                $service = new Service('Tencent', [
                    'key' => $this->_appConfig['akey'],
                    'secret' => $this->_appConfig['skey'],
                    'callback' => $redirectUri,
                    'scope' => $this->scope
                ]);
                $service->requestAuthorize();
                exit();
            }
        } catch (\Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    /**
     * 处理QQ的回调数据
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
            
            $code = isset($_GET['code']) ? trim($_GET['code']) : '';
            if (empty($code)) {
                throw new \Exception('code不能为空');
            }
            
            // 获取accessToken
            $service = new Service('Tencent', [
                'key' => $this->_appConfig['akey'],
                'secret' => $this->_appConfig['skey'],
                'callback' => 'http://' . $_SERVER["HTTP_HOST"]
            ]);
            $token = $service->getAccessToken();
            $access_token = $token->getTokenValue();
            
            // 获取用户OpenID
            $httpClient = new \Eva\EvaOAuth\AuthorizedHttpClient($token);
            $response = $httpClient->get('https://graph.qq.com/oauth2.0/me?access_token=' . $access_token);
            $openIdInfo = $this->getJson($response->getBody());
            if (isset($openIdInfo['error'])) {
                throw new \Exception($openIdInfo['error'] . ":" . $openIdInfo['error_description']);
            }
            
            // 授权成功后，记录该用户的基本信息
            $openid = $openIdInfo['openid'];
            $response = $httpClient->get('https://graph.qq.com/user/get_user_info?access_token=' . $access_token . '&openid=' . $openid . '&appid=' . $this->_appConfig['akey']);
            $userInfo = $this->getJson($response->getBody());
            // { "ret":1002, "msg":"请先登录" }
            if (! empty($userInfo['ret'])) {
                throw new \Exception($userInfo['ret'] . ":" . $userInfo['msg']);
            }
            
            $userInfo['openid'] = $openid;
            $userInfo['nickname'] = ($userInfo['nickname']);
            $userInfo['headimgurl'] = ($userInfo['figureurl']);
            
            $_SESSION['iTencent']["accessToken_{$this->appid}_{$this->scope}"] = $userInfo;
            $path = '/';
            $expireTime = time() + 1.5 * 3600;
            setcookie("__iTencent_accessToken_{$this->appid}_{$this->scope}__", json_encode($userInfo), $expireTime, $path);
            
            $redirect = $this->getRedirectUrl($redirect, $userInfo);
            
            // die($redirect);
            header("location:{$redirect}");
            
            // 调整数据库操作的执行顺序，优化跳转速度
            fastcgi_finish_request();
            $this->_user->updateUserInfoBySns($userInfo['openid'], $userInfo);
            
            exit();
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
        // $redirect = $this->addUrlParameter($redirect, array(
        // 'userToken' => urlencode($arrAccessToken['access_token'])
        // ));
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
        return $redirect;
    }

    private function getJson($response)
    {
        // --------检测错误是否发生
        if (strpos($response, "callback") !== false) {
            
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        }
        
        $user = json_decode($response, true);
        return $user;
    }
}

