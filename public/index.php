<?php

use Phalcon\Mvc\Application;
use Phalcon\DI\FactoryDefault;
use Phalcon\DI;
// ini_set('display_errors', 'On');
// die('xxx:' . ini_get('display_errors'));
define('PHALCON_VERSION', \Phalcon\Version::get());
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
// error_reporting(- 1);

ini_set("session.save_handler", "memcached"); // 是memcached不是memcache
ini_set("session.save_path", "192.168.81.129:11211"); // 不要tcp:
// php7升级后session_start(): Unable to clear session lock record
if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
    ini_set('session.lazy_write', 0);
}

//https://docs.phalcon.io/4.0/en/upgrade
if (version_compare(PHALCON_VERSION, '4.0.0') < 0) {
    define('MYDB_FETCH_ASSOC', \Phalcon\Db::FETCH_ASSOC);
    define('MYDB_FETCH_OBJ', \Phalcon\Db::FETCH_OBJ);
} else {
    define('MYDB_FETCH_ASSOC', \Phalcon\Db\Enum::FETCH_ASSOC);
    define('MYDB_FETCH_OBJ', \Phalcon\Db\Enum::FETCH_OBJ);
}

try {
    session_start();
    /**
     * Include const
     */
    require __DIR__ . '/../config/const.php';

    // chmod(APP_PATH . "cache/", 777);

    define('CURRENT_TIMESTAMP', time()); // 当前时间

    require __DIR__ . '/../vendor/autoload.php';

    require __DIR__ . '/../library/functions.php';

    require __DIR__ . '/../library/iCache.php';

    require __DIR__ . '/../library/core.php';

    require __DIR__ . '/../library/MongoDate.php';

    require __DIR__ . '/../library/MongoId.php';

    require __DIR__ . '/../library/MongoRegex.php';
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

    // phalcon从3.4版本升级到4.0版本不兼容变化汇总 https://blog.csdn.net/ligaofeng/article/details/103837168/
    if (version_compare(PHALCON_VERSION, '4.0.0') < 0) {
        echo $application->handle()->getContent();
    } else {
        $request = new \Phalcon\Http\Request();
        $response = $application->handle($request->getURI());
        $response->send();
    }
} catch (\Exception $e) {
    die($e->getMessage());
}
