<?php
namespace App\Goods;

use Phalcon\Autoload\Loader;
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
        
        $loader->setNamespaces(array(
            'App\Goods\Controllers' => __DIR__ . '/controllers/',
        ));
        $loader->register();
    }

    /**
     * Registers the module-only Goodss
     *
     * @param \Phalcon\Di\Di $di
     */
    public function registerServices($di)
    {
        /**
         * Read configuration
         */
        $config = include __DIR__ . "/config/config.php";
        
        // Registering a dispatcher
        $di->set('dispatcher', function () {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("App\Goods\Controllers");
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
