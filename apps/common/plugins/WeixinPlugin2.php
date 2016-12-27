<?php
namespace App\Common\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

/**
 * WeixinPlugin2
 *
 * This is the Weixin plugin which controls that users only have access to the modules they're assigned to
 */
class WeixinPlugin2 extends Plugin
{

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event            
     * @param Dispatcher $dispatcher            
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        // 不是接口调用的话
        if (! $this->request->isAjax()) {
            // 对于cookie的操作
            $operation4cookie = trim($this->request->get('operation4cookie'));
            // 没有任何操作的话，一般的流程
            if (empty($operation4cookie)) {
                
                $actionName = $dispatcher->getActionName();
                $controllerName = $dispatcher->getControllerName();
                $moduleName = $dispatcher->getModuleName();
                
                // 增加需要微信授权的module和controller以及排除的actions
                $ruleList = array();
                // $ruleList["livecommunity"]['user'] = [
                // 'get-info'
                // ];
                
                $isNeed = false;
                foreach ($ruleList as $module => $rule) {
                    foreach ($rule as $controller => $excludeActions) {
                        if ($moduleName == $module && $controllerName == $controller && ! in_array($actionName, $excludeActions)) {
                            $isNeed = true;
                            break;
                        }
                    }
                }
                if ($isNeed || $actionName == 'weixinauthorizebefore') {
                    // 检查是否需要授权处理
                    $this->isAuthorized($dispatcher);
                }
            } elseif ($operation4cookie == 'store') { // 存储测试用的cookie
                $config = $this->getDI()->get('config');
                $secretKey = $config['weixinAuthorize']['secretKey'];
                $FromUserName = trim($this->request->get('FromUserName'));
                $nickname = trim($this->request->get('nickname'));
                $headimgurl = trim($this->request->get('headimgurl'));
                $timestamp = time();
                $signkey = sha1($FromUserName . "|" . $secretKey . "|" . $timestamp);
                
                $userInfo = array(
                    'FromUserName' => $FromUserName,
                    'nickname' => trim($nickname),
                    'headimgurl' => trim($headimgurl),
                    'signkey' => $signkey,
                    'timestamp' => $timestamp
                );
                // 存储微信信息到cookie
                setcookie('Weixin_userInfo', json_encode($userInfo), time() + 3600 * 24 * 30, '/');
                $_COOKIE['Weixin_userInfo'] = json_encode($userInfo);
                print_r($_COOKIE);
                die('cookie has been success to set');
            } elseif ($operation4cookie == 'clear') { // 情况测试用cookie
                
                setcookie('Weixin_userInfo', '', - 3600, '/');
                unset($_COOKIE['Weixin_userInfo']);
                unset($_SESSION['isAuthorizing']);
                print_r($_COOKIE);
                die('cookie has been success to clear');
            }
        }
    }

    /**
     * 是否已授权了
     */
    private function isAuthorized(Dispatcher $dispatcher)
    {
        $actionName = $dispatcher->getActionName();
        $moduleName = $dispatcher->getModuleName();
        // 是否需要授权
        $isAuthorizeNeeded = false;
        
        // 检查cookie有没有值
        $userInfo = empty($_COOKIE['Weixin_userInfo']) ? array() : json_decode($_COOKIE['Weixin_userInfo'], true);
        if (! empty($userInfo)) {
            // 检查cookie的有效性
            $config = $this->getDI()->get('config');
            $secretKey = $config['weixinAuthorize']['secretKey'];
            $FromUserName = trim($userInfo['FromUserName']);
            $nickname = trim($userInfo['nickname']);
            $headimgurl = trim($userInfo['headimgurl']);
            $timestamp = trim($userInfo['timestamp']);
            $signkey = trim($userInfo['signkey']);
            $isValid = empty($secretKey) || $this->validateOpenid($FromUserName, $timestamp, $secretKey, $signkey);
            // 无效的话
            if (! $isValid) {
                $userInfo = array();
            }
        }
        
        if (empty($userInfo)) {
            // 如果没有的话就需要授权
            $isAuthorizeNeeded = true;
            // 如果在进行授权处理中的话
            if (! empty($_SESSION['isAuthorizing'])) {
                if ($actionName == 'weixinauthorizecallback') {
                    $isAuthorizeNeeded = false;
                }
            }
        } else {
            if ($actionName == 'weixinauthorizebefore') {
                if (! empty($_SESSION['Weixin_callbackUrl'])) {
                    $callbackUrl = $_SESSION['Weixin_callbackUrl'];
                    header("Location:{$callbackUrl}");
                    exit();
                } else {
                    $isAuthorizeNeeded = true;
                }
            }
        }
        
        // 进行授权处理
        if ($isAuthorizeNeeded) {
            $this->doAuthorize($dispatcher);
        }
    }
    
    // 授权
    private function doAuthorize(Dispatcher $dispatcher)
    {
        try {
            $actionName = $dispatcher->getActionName();
            $controllerName = $dispatcher->getControllerName();
            $moduleName = $dispatcher->getModuleName();
            $scheme = $this->request->getScheme();
            
            // 回调地址的处理
            if ($actionName == 'weixinauthorizebefore') {
                $callbackUrl = $this->request->get('callbackUrl');
                if (empty($callbackUrl)) {
                    throw new \Exception('callbackUrl不能为空');
                }
            } else {
                $callbackUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
            }
            $_SESSION['Weixin_callbackUrl'] = $callbackUrl;
            $callbackUrl = "";
            // 正在进行授权的处理, 初始化信息
            setcookie('Weixin_userInfo', '', - 3600, '/');
            unset($_COOKIE['Weixin_userInfo']);
            $_SESSION['isAuthorizing'] = true;
            
            $config = $this->getDI()->get('config');
            $path = '/';
            $authorizeUrl = $config['weixinAuthorize']['authorizeUrl'];
            $scope = empty($config['weixinAuthorize']) ? 'snsapi_userinfo' : $config['weixinAuthorize']['scope'];
            
            $redirectUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}{$path}{$moduleName}/{$controllerName}/weixinauthorizecallback";
            if (! empty($callbackUrl)) {
                $redirectUrl .= "?callbackUrl={$callbackUrl}";
            }
            
            // 如果有什么特殊处理的话
            $redirectUrl = urlencode($redirectUrl);
            $url = "{$authorizeUrl}?scope={$scope}&redirect={$redirectUrl}";
            
            header("Location:{$url}");
            exit();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * 微信openid校验
     *
     * @param string $FromUserName            
     * @param string $timestamp            
     * @param string $secretKey            
     * @param string $signature            
     * @return boolean
     */
    private function validateOpenid($FromUserName, $timestamp, $secretKey, $signature)
    {
        $secret = sha1($FromUserName . "|" . $secretKey . "|" . $timestamp);
        if ($signature != $secret) {
            return false;
        } else {
            return true;
        }
    }
}
