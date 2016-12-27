<?php
namespace App\Campaign;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Manager as EventsManager;
use App\Common\Plugins\WeixinPlugin2;
use App\Common\Plugins\TerminatePlugin;

class Module
{

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders()
    {
        $loader = new Loader();
        
        $loader->registerNamespaces(array(
            'App\Campaign\Controllers' => __DIR__ . '/controllers/',
            'App\Campaign\Services' => __DIR__ . '/services/',
            'App\Campaign\Models' => __DIR__ . '/models/'
        ));
        $loader->register();
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di            
     */
    public function registerServices($di)
    {
        
        /**
         * Read configuration
         */
        $config = include __DIR__ . "/config/config.php";
        
        // Registering a dispatcher
        $di->set('dispatcher', function () {
            $eventsManager = new EventsManager();
            $eventsManager->attach('dispatch:beforeDispatch', new WeixinPlugin2());
            $eventsManager->attach('dispatch:beforeDispatch', new TerminatePlugin());
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("App\Campaign\Controllers");
            $dispatcher->setEventsManager($eventsManager);
            
            return $dispatcher;
        });
        
        /**
         * Setting up the view component
         */
        $di['view'] = function () {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');
            
            return $view;
        };
    }
}
