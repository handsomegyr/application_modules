<?php
namespace App\Mail;

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
            'App\Mail\Controllers' => __DIR__ . '/controllers/',
            'App\Mail\Models' => __DIR__ . '/models/',
            'App\Mail\Helpers' => __DIR__ . '/views/helpers/'
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
        $di->set('dispatcher', function ()
        {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("App\Mail\Controllers");
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
