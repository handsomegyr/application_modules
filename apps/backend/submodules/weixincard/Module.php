<?php

namespace App\Backend\Weixincard;

use Phalcon\Autoload\Loader;
use Phalcon\Mvc\View;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Events\Manager as EventsManager;
use App\Backend\Tags\MyTags;
use App\Backend\Plugins\SecurityPlugin;
use App\Backend\Plugins\NotFoundPlugin;
use Phalcon\Mvc\Url;

class Module
{

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders()
    {
        $loader = new Loader();

        $loader->setNamespaces(array(
            'App\Backend\Controllers' => APP_PATH . 'apps/backend/controllers/',
            'App\Backend\Tags' => APP_PATH . 'apps/backend/tags/',
            'App\Backend\Plugins' => APP_PATH . 'apps/backend/plugins/',
            'App\Backend\Submodules\Weixincard\Controllers' => __DIR__ . '/controllers/'
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
        $config = include APP_PATH . "apps/backend/config/config.php";
        $di->set('adminConfig', function () use ($config) {
            return $config;
        });
        // Registering a dispatcher
        $di->set('dispatcher', function () {
            $eventsManager = new EventsManager();

            /**
             * Check if the user is allowed to access certain action using the SecurityPlugin
             */
            $eventsManager->attach('dispatch:beforeDispatch', new SecurityPlugin());

            /**
             * Handle exceptions and not-found exceptions using NotFoundPlugin
             */
            $eventsManager->attach('dispatch:beforeException', new NotFoundPlugin());

            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("App\Backend\Submodules\Weixincard\Controllers");
            // $dispatcher->setModuleName($moduleName)
            $dispatcher->setEventsManager($eventsManager);
            return $dispatcher;
        });

        $di['myTag'] = function () {
            return new MyTags();
        };

        /**
         * Setting up the view component
         */
        $di['view'] = function () {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');
            $view->setLayoutsDir('../../../views/layouts/');
            $view->setPartialsDir('../../../views/');
            $view->setMainView('../../../views/index');

            $view->registerEngines(array(
                // ".volt" => 'volt'
                ".phtml" => 'volt'
            ));
            return $view;
        };

        /**
         * Setting up volt
         */
        //https://docs.phalcon.io/4.0/en/volt
        if (version_compare(PHALCON_VERSION, '4.0.0') < 0) {
            $di->set('volt', function ($view, $di) {

                $volt = new VoltEngine($view, $di);

                $volt->setOptions(array(
                    "compiledPath" => APP_PATH . "cache/volt/"
                ));

                $compiler = $volt->getCompiler();
                $compiler->addFunction('is_a', 'is_a');

                return $volt;
            }, true);
        } else {
            $di->set('volt', function (\Phalcon\Mvc\ViewBaseInterface $view) use ($di) {
                $volt = new VoltEngine($view, $di);
                $volt->setOptions(
                    [
                        'always'    => true,
                        // 'extension' => '.php',
                        'separator' => '_',
                        'stat'      => true,
                        // 'path'      => appPath('storage/cache/volt/'),
                        'path'      => APP_PATH . "cache/volt/",
                        // 'prefix'    => '-prefix-',
                    ]
                );
                return $volt;
            });
        }
    }
}
