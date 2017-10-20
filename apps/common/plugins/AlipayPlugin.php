<?php
namespace App\Common\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

/**
 * AlipayPlugin
 *
 * This is the alipay plugin which controls that users only have access to the modules they're assigned to
 */
class AlipayPlugin extends Plugin
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
                if ($isNeed || $actionName == 'alipayauthorizebefore') {
                    // 检查是否需要授权处理
                    $this->isAuthorized($dispatcher);
                }
            } elseif ($operation4cookie == 'store') { // 存储测试用的cookie
                $config = $this->getDI()->get('config');
                $secretKey = $config['alipayAuthorize']['secretKey'];
                $user_id = trim($this->request->get('user_id'));
                $nickname = trim($this->request->get('nickname'));
                $headimgurl = trim($this->request->get('headimgurl'));
                $timestamp = time();
                $signkey = sha1($user_id . "|" . $secretKey . "|" . $timestamp);
                
                $userInfo = array(
                    'user_id' => $user_id,
                    'nickname' => trim($nickname),
                    'headimgurl' => trim($headimgurl),
                    'signkey' => $signkey,
                    'timestamp' => $timestamp
                );
                // 存储微信信息到cookie
                setcookie('Alipay_userInfo', json_encode($userInfo), time() + 3600 * 24 * 30, '/');
                $_COOKIE['Alipay_userInfo'] = json_encode($userInfo);
                print_r($_COOKIE);
                die('cookie has been success to set');
            } elseif ($operation4cookie == 'clear') { // 情况测试用cookie
                
                setcookie('Alipay_userInfo', '', - 3600, '/');
                unset($_COOKIE['Alipay_userInfo']);
                unset($_SESSION['Alipay_isAuthorizing']);
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
        $userInfo = empty($_COOKIE['Alipay_userInfo']) ? array() : json_decode($_COOKIE['Alipay_userInfo'], true);
        if (! empty($userInfo)) {
            // 检查cookie的有效性
            $config = $this->getDI()->get('config');
            $secretKey = $config['alipayAuthorize']['secretKey'];
            $user_id = trim($userInfo['user_id']);
            $nickname = trim($userInfo['nickname']);
            $headimgurl = trim($userInfo['headimgurl']);
            $timestamp = trim($userInfo['timestamp']);
            $signkey = trim($userInfo['signkey']);
            $isValid = empty($secretKey) || $this->validateOpenid($user_id, $timestamp, $secretKey, $signkey);
            // 无效的话
            if (! $isValid) {
                $userInfo = array();
            }
        }
        
        if (empty($userInfo)) {
            // 如果没有的话就需要授权
            $isAuthorizeNeeded = true;
            // 如果在进行授权处理中的话
            if (! empty($_SESSION['Alipay_isAuthorizing'])) {
                if ($actionName == 'alipayauthorizecallback') {
                    $isAuthorizeNeeded = false;
                }
            }
        } else {
            if ($actionName == 'alipayauthorizebefore') {
                if (! empty($_SESSION['Alipay_callbackUrl'])) {
                    $callbackUrl = $_SESSION['Alipay_callbackUrl'];
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
            if ($actionName == 'alipayauthorizebefore') {
                $callbackUrl = $this->request->get('callbackUrl');
                if (empty($callbackUrl)) {
                    throw new \Exception('callbackUrl不能为空');
                }
            } else {
                $callbackUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
            }
            $_SESSION['Alipay_callbackUrl'] = $callbackUrl;
            $callbackUrl = "";
            // 正在进行授权的处理, 初始化信息
            setcookie('Alipay_userInfo', '', - 3600, '/');
            unset($_COOKIE['Alipay_userInfo']);
            $_SESSION['Alipay_isAuthorizing'] = true;
            
            $config = $this->getDI()->get('config');
            $path = '/';
            $authorizeUrl = $config['alipayAuthorize']['authorizeUrl'];
            $scope = empty($config['alipayAuthorize']) ? 'auth_user' : $config['alipayAuthorize']['scope'];
            
            $redirectUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}{$path}{$moduleName}/{$controllerName}/alipayauthorizecallback";
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
     * user_id校验
     *
     * @param string $user_id            
     * @param string $timestamp            
     * @param string $secretKey            
     * @param string $signature            
     * @return boolean
     */
    private function validateOpenid($user_id, $timestamp, $secretKey, $signature)
    {
        $secret = sha1($user_id . "|" . $secretKey . "|" . $timestamp);
        if ($signature != $secret) {
            return false;
        } else {
            return true;
        }
    }
}
