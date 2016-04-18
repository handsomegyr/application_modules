<?php
namespace App\Tencent\Controllers;

use Eva\EvaOAuth\Service;
use App\Tencent\Models\User;
use App\Tencent\Models\OauthInfo;
use App\Tencent\Models\Application;
use App\Tencent\Models\AppKey;
use Respect\Validation\Validator as v;

class SnsController extends ControllerBase
{

    private $_user;

    private $_app;

    private $_config;

    private $_tracking;

    private $_model;

    private $_appid;

    private $_appConfig;

    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
        
        // $this->_tracking = new ScriptTracking();
        $this->_user = new User();
        $this->_model = new OauthInfo();
        $this->_app = new Application();
        $this->_key = new AppKey();
        
        $this->_appid = $this->get('appid');
        if (empty($this->_appid)) {
            if (empty($_SESSION['appid'])) {
                exit("appid不能为空");
            } else {
                $this->_appid = $_SESSION['appid'];
            }
        }
        
        // 获取设置
        $this->_appConfig = $this->_app->getInfoById($this->_appid);
        if (empty($this->_appConfig) || empty($this->_appConfig['appKeyId'])) {
            exit("appid不正确");
        }
        
        // 初始化应用密钥
        $this->_appKey = $this->_key->getInfoById($this->_appConfig['appKeyId']);
        if (empty($this->_appKey)) {
            exit("appKey未设置");
        }
    }

    /**
     * http://webcms.didv.cn/tencent/sns/index?appid=563b20c5bbcb2605038b4568&redirect=http%3A%2F%2Fwwww.baidu.com%2F&state=1234
     * 引导用户去往登录授权
     */
    public function indexAction()
    {
        $_SESSION['oauth_start_time'] = getMilliTime();
        try {
            if (isset($_SESSION['iTencent']['accessToken'])) {
                $redirect = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : '';
                $arrAccessToken = $_SESSION['iTencent']['accessToken'];
                if (isset($arrAccessToken['openid'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'openid' => $arrAccessToken['openid']
                    ));
                }
                
                if (isset($arrAccessToken['umaId'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'umaId' => $arrAccessToken['umaId']
                    ));
                }
                
                if (isset($arrAccessToken['nickname'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'nickname' => $arrAccessToken['nickname']
                    ));
                }
                
                if (isset($arrAccessToken['figureurl'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'figureurl' => $arrAccessToken['figureurl']
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
                
                // $this->_tracking->record("授权session存在", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                header("location:{$redirect}");
                exit();
            } elseif (! empty($_COOKIE['Tencent[openid]'])) {
                $redirect = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : '';
                if (isset($_COOKIE['Tencent[openid]'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'openid' => $_COOKIE['Tencent[openid]']
                    ));
                }
                if (isset($_COOKIE['Tencent[umaId]'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'umaId' => $_COOKIE['Tencent[umaId]']
                    ));
                }
                
                if (isset($_COOKIE['Tencent[nickname]'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'nickname' => $_COOKIE['Tencent[nickname]']
                    ));
                }
                
                if (isset($_COOKIE['Tencent[figureurl]'])) {
                    $redirect = $this->addUrlParameter($redirect, array(
                        'figureurl' => $_COOKIE['Tencent[figureurl]']
                    ));
                }
                
                // 计算signkey
                $timestamp = time();
                $signkey = $this->getSignKey($_COOKIE['Tencent[openid]'], $timestamp);
                $redirect = $this->addUrlParameter($redirect, array(
                    'signkey' => $signkey
                ));
                $redirect = $this->addUrlParameter($redirect, array(
                    'timestamp' => $timestamp
                ));
                
                // $this->_tracking->record("授权cookie存在", $_SESSION['oauth_start_time'], microtime(true), $_COOKIE['Tencent[openid]']);
                header("location:{$redirect}");
                exit();
            } else {
                $redirect = isset($_GET['redirect']) ? urlencode(trim($_GET['redirect'])) : ''; // 附加参数存储跳转地址
                if (empty($redirect)) {
                    exit("回调地址未定义");
                }
                $state = isset($_GET['state']) ? trim($_GET['state']) : ''; // 状态字段，这里为4-32长度的字母和数字组合
                
                $path = '/';
                $scheme = $this->getRequest()->getScheme();
                $host = $this->getRequest()->getHttpHost();
                $moduleName = $this->moduleName;
                
                $redirect = urlencode($redirect);
                // $redirect_uri = "{$scheme}://{$host}{$path}{$moduleName}/sns/callback?appid={$this->_appid}&callbackUrl={$redirect}";
                $redirect_uri = "{$scheme}://{$host}{$path}{$moduleName}/sns/callback";
                $_SESSION['appid'] = $this->_appid;
                $_SESSION['callbackUrl'] = $redirect;
                
                // 初始化QQ适配器
                $service = new Service('Tencent', [
                    'key' => $this->_appKey['akey'],
                    'secret' => $this->_appKey['akey'],
                    'callback' => $redirect_uri
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
            if (isset($_GET['code'])) {
                
                $redirect = isset($_SESSION['callbackUrl']) ? urldecode($_SESSION['callbackUrl']) : '';
                if (empty($redirect)) {
                    throw new Exception("回调地址未定义");
                }
                
                // 获取accessToken
                $service = new Service('Tencent', [
                    'key' => $this->_appKey['akey'],
                    'secret' => $this->_appKey['akey'],
                    'callback' => $redirect
                ]);
                
                // $accessToken = $service->getAccessToken();
                // var_dump($accessToken);
                $tokenAndUserInfo = $service->getTokenAndUser();
                var_dump($tokenAndUserInfo);
                die('code' . $_GET['code']);
                
                $httpClient = new \Eva\EvaOAuth\AuthorizedHttpClient($token);
                $response = $httpClient->get('https://graph.facebook.com/me');
                
                // 记录授权ID
                $umaId = $this->_model->record(($this->_appConfig['_id']), $accessToken);
                
                if (! empty($accessToken['remoteToken'])) {
                    $userInfo = array();
                    $userInfo['openid'] = $accessToken['remoteToken'];
                    $userInfo['nickname'] = $accessToken['remoteUserName'];
                    $userInfo['figureurl'] = $accessToken['remoteImageUrl'];
                    $userInfo['access_token'] = $accessToken;
                    $this->_user->updateUserInfo($userInfo['openid'], $userInfo);
                    
                    // 记录SESSION
                    $arrAccessToken['umaId'] = $umaId;
                    $arrAccessToken['nickname'] = $userInfo['nickname'];
                    $arrAccessToken['figureurl'] = urlencode($userInfo['figureurl']);
                    
                    $_SESSION['iTencent']['accessToken'] = $arrAccessToken;
                    
                    $path = $this->_config['global']['path'];
                    setcookie('Tencent[openid]', $arrAccessToken['openid'], time() + 365 * 24 * 3600, $path);
                    setcookie('Tencent[umaId]', $arrAccessToken['umaId'], time() + 365 * 24 * 3600, $path);
                    setcookie('Tencent[nickname]', $arrAccessToken['nickname'], time() + 365 * 24 * 3600, $path);
                    setcookie('Tencent[figureurl]', $arrAccessToken['figureurl'], time() + 365 * 24 * 3600, $path);
                    
                    $redirect = $this->addUrlParameter($redirect, array(
                        'openid' => $arrAccessToken['openid']
                    ));
                    
                    $redirect = $this->addUrlParameter($redirect, array(
                        'umaId' => $arrAccessToken['umaId']
                    ));
                    
                    $redirect = $this->addUrlParameter($redirect, array(
                        'nickname' => $arrAccessToken['nickname']
                    ));
                    
                    $redirect = $this->addUrlParameter($redirect, array(
                        'figureurl' => $arrAccessToken['figureurl']
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
                }
                
                // $this->_tracking->record("QQ授权", $_SESSION['oauth_start_time'], microtime(true), $arrAccessToken['openid']);
                header("location:{$redirect}");
                exit();
            }
        } catch (Exception $e) {
            print_r($e->getFile());
            print_r($e->getLine());
            print_r($e->getMessage());
        }
    }

    private function addUrlParameter($url, array $params)
    {
        if (! empty($params)) {
            foreach ($params as $key => $value) {
                if (strpos($url, $key) === false) {
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

