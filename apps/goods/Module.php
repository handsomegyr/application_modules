<?php
namespace Webcms\Goods;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;

class Module
{

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders()
    {
        $loader = new Loader();
        
        $loader->registerNamespaces(array(
            'Webcms\Goods\Controllers' => __DIR__ . '/controllers/',
            'Webcms\Goods\Models' => __DIR__ . '/models/',
            'Webcms\Goods\Service' => __DIR__ . '/service/',
            'Webcms\Goods\Helpers' => __DIR__ . '/views/helpers/'
        ));
        $loader->register();
    }

    /**
     * Registers the module-only Goodss
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
        $di->set('dispatcher', function ()
        {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("Webcms\Goods\Controllers");
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
