<?php
namespace App\Common\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

/**
 * 活动结束控制器
 *
 * @author Kan
 *        
 */
class TerminatePlugin extends Plugin
{

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event            
     * @param Dispatcher $dispatcher            
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        $actionName = strtolower($dispatcher->getActionName());
        $controllerName = strtolower($dispatcher->getControllerName());
        $moduleName = strtolower($dispatcher->getModuleName());
        
        if (false && in_array($moduleName, array(
            'starwar'
        )) && in_array($controllerName, array(
            'index'
        ))) {
            $msg = '对不起,你来晚了,活动已结束!';
            $code = "-9999";
            if ($this->request->isAjax()) {
                if ($actionName != 'usecoupon') {
                    $jsonpcallback = trim($this->request->get('jsonpcallback', ''));
                    die(jsonpcallback($jsonpcallback, false, $msg, $code));
                }
            } else {
                if ($actionName != 'subscibe' && $actionName != 'authorize' && $actionName != 'tips') {
                    $url = $this->url->get("{$moduleName}/{$controllerName}/tips");
                    $this->response->redirect($url, true, 302)->send();
                    exit();
                    // die($msg);
                }
            }
        }
    }

    public function __destruct()
    {}
}