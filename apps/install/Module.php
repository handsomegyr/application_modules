<?php
namespace App\Install;

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
            'App\Install\Controllers' => __DIR__ . '/controllers/',
            'App\Install\Tags' => __DIR__ . '/tags/'
        ));
        $loader->register();
    }

    /**
     * Registers the module-only services
     *
     * @param \Phalcon\Di\Di $di
     */
    public function registerServices($di)
    {
        /**
         * Read configuration
         */
        $config = include __DIR__ . "/config/config.php";
        
        $di->set('install_config', function () use($config) {
            return $config;
        });
        
        /**
         * Setting up the lang component
         */
        $di['lang'] = function ($key) use($config) {
            return isset($config[$key]) ? $config[$key] : '';
        };
        
        // Registering a dispatcher
        $di->set('dispatcher', function () {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("App\Install\Controllers");
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
