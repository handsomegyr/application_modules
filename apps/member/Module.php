<?php
namespace App\Member;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Manager as EventsManager;
use App\Common\Plugins\WeixinPlugin;
use App\Common\Plugins\TencentPlugin;

class Module
{

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders()
    {
        $loader = new Loader();
        
        $loader->registerNamespaces(array(
            'App\Member\Controllers' => __DIR__ . '/controllers/',
            'App\Member\Models' => __DIR__ . '/models/',
            'App\Member\Services' => __DIR__ . '/services/'
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
        define('SUBDOMAIN_SUFFIX', "");
        define('SESSION_EXPIRE', 20 * 60);
        define('COOKIE_PRE', '');
        define('TIMESTAMP', time());
        define('MD5_KEY', md5('shopnc'));
        
        /**
         * Read configuration
         */
        $config = include __DIR__ . "/config/config.php";
        
        // Registering a dispatcher
        $di->set('dispatcher', function ()
        {
            $eventsManager = new EventsManager();
            $eventsManager->attach('dispatch:beforeDispatch', new WeixinPlugin());
            $eventsManager->attach('dispatch:beforeDispatch', new TencentPlugin());
            
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("App\Member\Controllers");
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        });
        
        /**
         * Setting up the view component
         */
        $di['view'] = function ()
        {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');
            
            return $view;
        };
    }
}
