<?php
use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;
use Phalcon\DI;

ini_set("display_errors", "On");

error_reporting(E_ALL);

try {
    session_start();
    
    /**
     * Include const
     */    
    require __DIR__ . '/../config/const.php';
    
    
    require __DIR__ . '/../vendor/autoload.php';
    
    require __DIR__ . '/../library/functions.php';
    
    require __DIR__ . '/../library/core.php';
    
    /**
     * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
     */
    $di = new FactoryDefault();
    
    /**
     * Include services
     */
    require __DIR__ . '/../config/services.php';
    
    // Add any needed services to the DI here
    DI::reset();
    DI::setDefault($di);
    
    /**
     * Handle the request
     */
    $application = new Application($di);
    
    /**
     * Include modules
     */
    require __DIR__ . '/../config/modules.php';
    
    echo $application->handle()->getContent();
} catch (\Exception $e) {
    die($e->getMessage());
}
