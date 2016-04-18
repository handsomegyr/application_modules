<?php
namespace App\Backend\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;

/**
 * NotFoundPlugin
 *
 * Handles not-found controller/actions
 */
class NotFoundPlugin extends Plugin
{

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event            
     * @param Dispatcher $dispatcher            
     */
    public function beforeException(Event $event, MvcDispatcher $dispatcher,\Exception $exception)
    {
        if ($exception instanceof DispatcherException) {
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward(array(
                        'controller' => 'error',
                        'action' => 'show404'
                    ));
                    return false;
            }
        }
        
        if (empty($_SESSION['admin_id'])) {
            $this->view->setVar('errorMsg', $exception->getMessage());
            $dispatcher->forward(array(
                'controller' => 'error',
                'action' => 'show500'
            ));
        } else {
            $this->view->setVar('msg_type', 500);
            $this->view->setVar('msg_detail', $exception->getMessage());
            $this->view->setVar('auto_redirect', 1);
            $this->view->setVar('links', array());
            $dispatcher->forward(array(
                'controller' => 'error',
                'action' => 'message'
            ));
        }
        return false;
    }
}
