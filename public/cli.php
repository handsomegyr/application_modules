<?php
use Phalcon\DI\FactoryDefault\CLI as CliDI;
use Phalcon\CLI\Console as ConsoleApp;

try {
    /**
     * Include const
     */
    require __DIR__ . '/../config/const.php';
    
    defined('IS_IN_CLI_MODE') || define('IS_IN_CLI_MODE', true);
    
    require __DIR__ . '/../vendor/autoload.php';    

    require __DIR__ . '/../library/DatabaseManager.php';
    
    require __DIR__ . '/../library/functions.php';
    
    require __DIR__ . '/../library/core.php';
    
    require __DIR__ . '/../library/MongoDate.php';
    
    require __DIR__ . '/../library/MongoId.php';
    
    require __DIR__ . '/../library/MongoRegex.php';
    
    // Using the CLI factory default services container
    $di = new CliDI();
    
    /**
     * Include services
     */
    require __DIR__ . '/../config/services.php';
    
    /**
     * Register the autoloader and tell it to register the tasks directory
     */
    $loader = new \Phalcon\Loader();
    $loader->registerDirs(array(
        APP_PATH . 'apps/cronjob'
    ));
    $loader->register();
    /**
     * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
     */
    
    // Create a console application
    $application = new ConsoleApp();
    $application->setDI($di);
    
    /**
     * Include modules
     */
    require __DIR__ . '/../config/modules.php';
    
    /**
     * Process the console arguments
     */
    $arguments = array();
    $params = array();
    
    foreach ($argv as $k => $arg) {
        if ($k == 1) {
            $arguments['task'] = $arg;
        } elseif ($k == 2) {
            $arguments['action'] = $arg;
        } elseif ($k >= 3) {
            $params[] = $arg;
        }
    }
    if (count($params) > 0) {
        $arguments['params'] = $params;
    }
    
    // define global constants for the current task and action
    define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
    define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));
    
    // handle incoming arguments
    $application->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}

