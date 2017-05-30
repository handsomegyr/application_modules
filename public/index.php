<?php
use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;
use Phalcon\DI;
// ini_set('display_errors', 'On');
// die('xxx:' . ini_get('display_errors'));
error_reporting(E_ALL);
// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(- 1);

ini_set("session.save_handler", "memcached"); // 是memcached不是memcache
ini_set("session.save_path", "127.0.0.1:11211"); // 不要tcp:

try {
    session_start();
    /**
     * Include const
     */
    require __DIR__ . '/../config/const.php';
    define('CURRENT_TIMESTAMP', time()); // 当前时间
    
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
