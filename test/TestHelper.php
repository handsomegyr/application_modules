<?php
use Phalcon\DI;
use Phalcon\DI\FactoryDefault;

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT_PATH', __DIR__);
define('PATH_LIBRARY', __DIR__ . '/../library/');
define('APP_PATH', __DIR__ . '/../');
// define('PATH_SERVICES', __DIR__ . '/../app/services/');
// define('PATH_RESOURCES', __DIR__ . '/../app/resources/');

set_include_path(ROOT_PATH . PATH_SEPARATOR . get_include_path());

// Required for phalcon/incubator
include __DIR__ . "/../vendor/autoload.php";
include __DIR__ . "/UnitTestCase.php";

// Use the application autoloader to autoload the classes
// Autoload the dependencies found in composer
$loader = new \Phalcon\Loader();

$loader->registerDirs(array(
    ROOT_PATH
));

/**
$loader->registerNamespaces(array(
    'App\Common\Models' => APP_PATH . 'apps/common/models/base/',
    'App\Common\Models\Base\Mongodb' => APP_PATH . 'apps/common/models/base/mongodb/',
    'App\Common\Models\Base\Mysql' => APP_PATH . 'apps/common/models/base/mysql/',
    'App\Common\Models\Weixin\Mysql' => APP_PATH . 'apps/common/models/weixin/mysql/',
    'App\Common\Models\Weixin\Mongodb' => APP_PATH . 'apps/common/models/weixin/mongodb/',
    'App\Weixin\Models' => APP_PATH . 'apps/weixin/models/',
    'App\Lottery\Models' => APP_PATH . 'apps/lottery/models/'
));

$loader->register();
**/

$di = new FactoryDefault();
DI::reset();

// Add any needed services to the DI here
DI::setDefault($di);