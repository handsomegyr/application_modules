<?php
namespace App\Common\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

/**
 * WeixinPlugin
 *
 * This is the Weixin plugin which controls that users only have access to the modules they're assigned to
 */
class WeixinPlugin extends Plugin
{

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event            
     * @param Dispatcher $dispatcher            
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $actionName = $dispatcher->getActionName();
        $controllerName = $dispatcher->getControllerName();
        $moduleName = $dispatcher->getModuleName();
        
        // 不是接口调用的话
        if (! $this->request->isAjax()) {
            $istest = trim($this->request->get('istest'));
            if (empty($istest)) {
                // 增加需要微信授权的module和controller以及排除的actions
                $ruleList = array();
                $ruleList["spicy"]['index'] = array(
                    'rule'
                );
                $ruleList["christmas"]['index'] = array(
                    'rule',
                    'getlist',
                    'doexchange',
                    'doexchangerecord',
                    'getinvitation',
                    'usecoupon',
                    'subscibe'
                );
                
                $ruleList["tuanyuan"]['index'] = array(
                    'rule',
                    'index',
                    'subscribe',
                    'getinvitation',
                    'getlist',
                    'createinvitation',
                    'dolottery',
                    'sendtemplatemsg',
                    'weixinauthorize'
                );
                
                $isNeed = false;
                foreach ($ruleList as $module => $rule) {
                    foreach ($rule as $controller => $excludeActions) {
                        $excludeActions = array_merge($excludeActions, array(
                            'getcachekey',
                            'clearcache',
                            'getaccesstoken'
                        ));
                        if ($moduleName == $module && $controllerName == $controller && ! in_array($actionName, $excludeActions)) {
                            $isNeed = true;
                            break;
                        }
                    }
                }
                if ($isNeed) {
                    // 检查是否需要授权处理
                    $this->isAuthorized($dispatcher);
                }
            } else {
                $FromUserName = trim($this->request->get('FromUserName'));
                $nickname = trim($this->request->get('nickname'));
                $headimgurl = trim($this->request->get('headimgurl'));
                $userInfo = array(
                    'user_id' => $FromUserName,
                    'user_name' => urldecode($nickname),
                    'user_headimgurl' => urldecode($headimgurl),
                    'subscribe' => 1
                );
                // 存储微信id到session
                $_SESSION['Weixin_userInfo'] = $userInfo;
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
        
        // 检查session中有没有值
        $userInfo = empty($_SESSION['Weixin_userInfo']) ? array() : $_SESSION['Weixin_userInfo'];
        if (empty($userInfo)) {
            // 如果没有的话就需要授权
            $isAuthorizeNeeded = true;
            // 如果在进行授权处理中的话
            if (! empty($_SESSION['isWeixinAuthorizing'])) {
                if ($actionName == 'weixinauthorize') {
                    $isAuthorizeNeeded = false;
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
        $config = $this->getDI()->get('config');
        $path = '/';
        // 正在进行授权的处理
        unset($_SESSION['Weixin_userInfo']);
        $_SESSION['isWeixinAuthorizing'] = true;
        
        $actionName = $dispatcher->getActionName();
        $controllerName = $dispatcher->getControllerName();
        $moduleName = $dispatcher->getModuleName();
        
        $scheme = $this->request->getScheme();
        if ($actionName == 'weixinauthorize') {
            $callbackUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}{$path}{$moduleName}/{$controllerName}/index";
        } else {
            $callbackUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        }
        $callbackUrl = urlencode($callbackUrl);
        $redirectUrl = "{$scheme}://{$_SERVER['HTTP_HOST']}{$path}{$moduleName}/{$controllerName}/weixinauthorize?callbackUrl={$callbackUrl}";
        // $redirectUrl = urlencode($redirectUrl);
        
        $authorizeUrl = $config['weixinAuthorize']['authorizeUrl'];
        $scope = empty($config['weixinAuthorize']) ? 'snsapi_userinfo' : $config['weixinAuthorize']['scope'];
        // 如果有什么特殊处理的话
        if ($moduleName == "spicy") {
            // $authorizeUrl="xxx"
            // $scope="xxx"
            // $secretKey = "xxx";
        }
        $url = "{$authorizeUrl}?scope={$scope}&redirect={$redirectUrl}";
        header("Location:{$url}");
        exit();
    }
}
