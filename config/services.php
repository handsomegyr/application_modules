<?php

/**
 * Services are globally registered in this file
 */
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Loader;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
// use Phalcon\Cache\Backend\File as BackFile;
use Phalcon\Cache\Backend\Libmemcached as Libmemcached;
use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;
use Phalcon\Events\Manager as EventsManager;
use Pheanstalk\Pheanstalk;

registerAutoloaders();

registerServices($di);

/**
 * Registers the module auto-loader
 */
function registerAutoloaders()
{
    $loader = new Loader();
    $loader->registerNamespaces(
        array(
            'App\Common\Models' => APP_PATH . 'apps/common/models/base/',
            'App\Common\Models\Mongodb' => APP_PATH . 'apps/common/models/base/mongodb/',
            'App\Common\Models\Mysql' => APP_PATH . 'apps/common/models/base/mysql/',
            
            'App\Common\Models\Mysql\Goods' => APP_PATH . 'apps/common/models/goods/mysql/',
            'App\Common\Models\Mongodb\Goods' => APP_PATH . 'apps/common/models/goods/mongodb/',
            'App\Common\Models\Goods' => APP_PATH . 'apps/common/models/goods/',
            
            'App\Common\Models\Mysql\Lottery' => APP_PATH . 'apps/common/models/lottery/mysql/',
            'App\Common\Models\Mongodb\Lottery' => APP_PATH . 'apps/common/models/lottery/mongodb/',
            'App\Common\Models\Lottery' => APP_PATH . 'apps/common/models/lottery/',
            
            'App\Common\Models\Mysql\Mail' => APP_PATH . 'apps/common/models/mail/mysql/',
            'App\Common\Models\Mongodb\Mail' => APP_PATH . 'apps/common/models/mail/mongodb/',
            'App\Common\Models\Mail' => APP_PATH . 'apps/common/models/mail/',
            
            'App\Common\Models\Mysql\Message' => APP_PATH . 'apps/common/models/message/mysql/',
            'App\Common\Models\Mongodb\Message' => APP_PATH . 'apps/common/models/message/mongodb/',
            'App\Common\Models\Message' => APP_PATH . 'apps/common/models/message/',
            
            'App\Common\Models\Mysql\Payment' => APP_PATH . 'apps/common/models/payment/mysql/',
            'App\Common\Models\Mongodb\Payment' => APP_PATH . 'apps/common/models/payment/mongodb/',
            'App\Common\Models\Payment' => APP_PATH . 'apps/common/models/payment/',
            
            'App\Common\Models\Mysql\Points' => APP_PATH . 'apps/common/models/points/mysql/',
            'App\Common\Models\Mongodb\Points' => APP_PATH . 'apps/common/models/points/mongodb/',
            'App\Common\Models\Points' => APP_PATH . 'apps/common/models/points/',
            
            'App\Common\Models\Mysql\Sms' => APP_PATH . 'apps/common/models/sms/mysql/',
            'App\Common\Models\Mongodb\Sms' => APP_PATH . 'apps/common/models/sms/mongodb/',
            'App\Common\Models\Sms' => APP_PATH . 'apps/common/models/sms/',
            
            'App\Common\Models\Mysql\System' => APP_PATH . 'apps/common/models/system/mysql/',
            'App\Common\Models\Mongodb\System' => APP_PATH . 'apps/common/models/system/mongodb/',
            'App\Common\Models\System' => APP_PATH . 'apps/common/models/system/',
            
            'App\Common\Models\Mysql\Tencent' => APP_PATH . 'apps/common/models/tencent/mysql/',
            'App\Common\Models\Mongodb\Tencent' => APP_PATH . 'apps/common/models/tencent/mongodb/',
            'App\Common\Models\Tencent' => APP_PATH . 'apps/common/models/tencent/',
            
            'App\Common\Models\Mysql\Weixin' => APP_PATH . 'apps/common/models/weixin/mysql/',
            'App\Common\Models\Mongodb\Weixin' => APP_PATH . 'apps/common/models/weixin/mongodb/',
            'App\Common\Models\Weixin' => APP_PATH . 'apps/common/models/weixin/',
            
            'App\Common\Models\Mysql\Member' => APP_PATH . 'apps/common/models/member/mysql/',
            'App\Common\Models\Mongodb\Member' => APP_PATH . 'apps/common/models/member/mongodb/',
            'App\Common\Models\Member' => APP_PATH . 'apps/common/models/member/',
            
            'App\Common\Models\Mysql\Order' => APP_PATH . 'apps/common/models/order/mysql/',
            'App\Common\Models\Mongodb\Order' => APP_PATH . 'apps/common/models/order/mongodb/',
            'App\Common\Models\Order' => APP_PATH . 'apps/common/models/order/',
            
            'App\Common\Models\Mysql\Store' => APP_PATH . 'apps/common/models/store/mysql/',
            'App\Common\Models\Mongodb\Store' => APP_PATH . 'apps/common/models/store/mongodb/',
            'App\Common\Models\Store' => APP_PATH . 'apps/common/models/store/',
            
            'App\Common\Models\Mysql\System' => APP_PATH . 'apps/common/models/system/mysql/',
            'App\Common\Models\Mongodb\System' => APP_PATH . 'apps/common/models/system/mongodb/',
            'App\Common\Models\System' => APP_PATH . 'apps/common/models/system/',
            
            'App\Common\Models\Mysql\Message' => APP_PATH . 'apps/common/models/message/mysql/',
            'App\Common\Models\Mongodb\Message' => APP_PATH . 'apps/common/models/message/mongodb/',
            'App\Common\Models\Message' => APP_PATH . 'apps/common/models/message/',
            
            'App\Common\Models\Mysql\Prize' => APP_PATH . 'apps/common/models/prize/mysql/',
            'App\Common\Models\Mongodb\Prize' => APP_PATH . 'apps/common/models/prize/mongodb/',
            'App\Common\Models\Prize' => APP_PATH . 'apps/common/models/prize/',
            
            'App\Common\Models\Mysql\Invitation' => APP_PATH . 'apps/common/models/invitation/mysql/',
            'App\Common\Models\Mongodb\Invitation' => APP_PATH . 'apps/common/models/invitation/mongodb/',
            'App\Common\Models\Invitation' => APP_PATH . 'apps/common/models/invitation/',
            
            'App\Common\Models\Mysql\Post' => APP_PATH . 'apps/common/models/post/mysql/',
            'App\Common\Models\Mongodb\Post' => APP_PATH . 'apps/common/models/post/mongodb/',
            'App\Common\Models\Post' => APP_PATH . 'apps/common/models/post/',
            
            'App\Common\Models\Mysql\Site' => APP_PATH . 'apps/common/models/site/mysql/',
            'App\Common\Models\Mongodb\Site' => APP_PATH . 'apps/common/models/site/mongodb/',
            'App\Common\Models\Site' => APP_PATH . 'apps/common/models/site/',
            
            'App\Common\Models\Mysql\Article' => APP_PATH . 'apps/common/models/article/mysql/',
            'App\Common\Models\Mongodb\Article' => APP_PATH . 'apps/common/models/article/mongodb/',
            'App\Common\Models\Article' => APP_PATH . 'apps/common/models/article/',
            
            'App\Common\Models\Mysql\Freight' => APP_PATH . 'apps/common/models/freight/mysql/',
            'App\Common\Models\Mongodb\Freight' => APP_PATH . 'apps/common/models/freight/mongodb/',
            'App\Common\Models\Freight' => APP_PATH . 'apps/common/models/freight/',
            
            'App\Common\Models\Mysql\Task' => APP_PATH . 'apps/common/models/task/mysql/',
            'App\Common\Models\Mongodb\Task' => APP_PATH . 'apps/common/models/task/mongodb/',
            'App\Common\Models\Task' => APP_PATH . 'apps/common/models/task/',
            
            'App\Common\Models\Mysql\Vote' => APP_PATH . 'apps/common/models/vote/mysql/',
            'App\Common\Models\Mongodb\Vote' => APP_PATH . 'apps/common/models/vote/mongodb/',
            'App\Common\Models\Vote' => APP_PATH . 'apps/common/models/vote/',
            
            'App\Common\Models\Mysql\Weixinredpack' => APP_PATH . 'apps/common/models/weixinredpack/mysql/',
            'App\Common\Models\Mongodb\Weixinredpack' => APP_PATH . 'apps/common/models/weixinredpack/mongodb/',
            'App\Common\Models\Weixinredpack' => APP_PATH . 'apps/common/models/weixinredpack/',
            
            'App\Common\Controllers' => APP_PATH . 'apps/common/controllers/',
            
            'App\Weixin\Models' => APP_PATH . 'apps/weixin/models/',
            'App\Points\Models' => APP_PATH . 'apps/points/models/',
            'App\Order\Models' => APP_PATH . '/apps/order/models/',
            'App\Goods\Models' => APP_PATH . '/apps/goods/models/',
            'App\Store\Models' => APP_PATH . 'apps/store/models/',
            'App\Member\Models' => APP_PATH . '/apps/member/models/',
            'App\Post\Models' => APP_PATH . '/apps/post/models/',
            'App\Site\Models' => APP_PATH . '/apps/site/models/',
            'App\Message\Models' => APP_PATH . '/apps/message/models/',
            'App\Payment\Models' => APP_PATH . '/apps/payment/models/',
            
            'App\Invitation\Models' => APP_PATH . 'apps/invitation/models/',
            'App\Prize\Models' => APP_PATH . 'apps/prize/models/',
            'App\Lottery\Models' => APP_PATH . 'apps/lottery/models/',
            'App\System\Models' => APP_PATH . 'apps/system/models/',
            'App\Article\Models' => APP_PATH . 'apps/article/models/',
            'App\Freight\Models' => APP_PATH . 'apps/freight/models/',
            'App\Task\Models' => APP_PATH . 'apps/task/models/',
            'App\Sms\Models' => APP_PATH . 'apps/sms/models/',
            'App\Mail\Models' => APP_PATH . 'apps/mail/models/',
            'App\Vote\Models' => APP_PATH . 'apps/vote/models/',
            'App\Weixinredpack\Models' => APP_PATH . 'apps/weixinredpack/models/',
            
            'App\Lottery\Services' => APP_PATH . 'apps/lottery/services/',
            'App\Order\Services' => APP_PATH . '/apps/order/services/',
            'App\Member\Services' => APP_PATH . '/apps/member/services/',
            'App\Goods\Services' => APP_PATH . '/apps/goods/services/',
            'App\Payment\Services' => APP_PATH . '/apps/payment/services/',
            'App\Weixinredpack\Services' => APP_PATH . '/apps/weixinredpack/services/',
            
            'App\Points\Helpers' => APP_PATH . 'apps/points/views/helpers/',
            'App\Goods\Helpers' => APP_PATH . 'apps/goods/views/helpers/',
            'App\System\Helpers' => APP_PATH . 'apps/system/views/helpers/',
            'App\Member\Helpers' => APP_PATH . 'apps/member/views/helpers/',
            'App\Order\Helpers' => APP_PATH . 'apps/order/views/helpers/',
            'App\Site\Helpers' => APP_PATH . 'apps/site/views/helpers/',
            'App\Post\Helpers' => APP_PATH . 'apps/post/views/helpers/',
            'App\Article\Helpers' => APP_PATH . 'apps/article/views/helpers/',
            'App\Freight\Helpers' => APP_PATH . 'apps/freight/views/helpers/',
            'App\Task\Helpers' => APP_PATH . 'apps/task/views/helpers/',
            
            'App\Common\Plugins' => APP_PATH . 'apps/common/plugins/'
        ));
    
    /**
     * We're a registering a set of directories taken from the configuration file
     */
    $loader->registerDirs(array(
        APP_PATH . 'library/'
    ));
    $loader->register();
}

/**
 * Registers the module-only services
 *
 * @param Phalcon\DI $di            
 */
function registerServices($di)
{
    /**
     * Read configuration
     */
    $config = include APP_PATH . "apps/common/config/config.php";
    $di->set('config', function () use($config) {
        return $config;
    });
    
    /**
     * Setting up the view component
     */
    $di['errors'] = function () use($config) {
        return $config['errors'];
    };
    
    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    $di['db'] = function () use($config) {
        $connection = new DbAdapter(array(
            "host" => $config->database->host,
            "username" => $config->database->username,
            "password" => $config->database->password,
            "dbname" => $config->database->dbname,
            "charset" => $config->database->charset
        ));
        // $connection->execute("SET NAMES 'utf8';");
        $eventsManager = new EventsManager();
        $eventsManager->attach('db', function ($event, $conn) {
            // echo $conn->getSQLStatement() . '<br />';
        });
        $connection->setEventsManager($eventsManager);
        return $connection;
    };
    
    /**
     * Database connection is created based in the parameters defined in the configuration file
     */
    $di['dbfrom'] = function () use($config) {
        $connection = new DbAdapter(array(
            "host" => $config->databasefrom->host,
            "username" => $config->databasefrom->username,
            "password" => $config->databasefrom->password,
            "dbname" => $config->databasefrom->dbname,
            "charset" => $config->database->charset
        ));
        // $connection->execute("SET NAMES 'utf8';");
        return $connection;
    };
    
    $di->setShared('transactions', function () {
        return new TransactionManager();
    });
    
    /**
     * Register a database component
     */
    $di->set('databases', function () {
        $default = new \iDatabase('54602cae489619970f8b4b58', 'guoyongrong0123456789', '54602cde4896197a0e8b4c5a');
        return array(
            "default" => $default
        );
    });
    
    /**
     * Setting up the pheanstalk queue component
     */
    $di['pheanstalk'] = function () use($config) {
        $pheanstalk = new Pheanstalk('127.0.0.1');
        return $pheanstalk;
    };
    
    /**
     * Setting up the cache component
     */
    $di['cache'] = function () use($config) {
        // Cache the files for 2 days using a Data frontend
        $frontCache = new FrontData(array(
            "lifetime" => 172800
        ));
        
        // // Create the component that will cache "Data" to a "File" backend
        // // Set the cache file directory - important to keep the "/" at the end of
        // // of the value for the folder
        // $cache = new BackFile($frontCache, array(
        // "cacheDir" => APP_PATH . "cache/service/"
        // ));
        
        // Create the Cache setting memcached connection options
        $cache = new Libmemcached($frontCache, array(
            'servers' => array(
                array(
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 1
                )
            ),
            'client' => array(
                Memcached::OPT_HASH => Memcached::HASH_MD5,
                Memcached::OPT_PREFIX_KEY => 'webcms.'
            )
        ));
        return $cache;
    };
    
    /**
     * Setting up the memcached component
     */
    $di['memcached'] = function () use($config) {
        $objMemcached = new \Memcached();
        $parameters = array();
        $memcacheConfig = array();
        $_SERVER['ICC_MEMCACHED_SERVER'] = "127.0.0.1:11211";
        if (! empty($_SERVER['ICC_MEMCACHED_SERVER'])) {
            $memcacheServers = explode(',', $_SERVER['ICC_MEMCACHED_SERVER']);
            if (is_array($memcacheServers) && ! empty($memcacheServers)) {
                $weight = floor(100 / count($memcacheServers));
                foreach ($memcacheServers as $server) {
                    $server = trim($server);
                    list ($host, $port) = explode(':', $server);
                    $parameters[] = array(
                        $host,
                        $port,
                        $weight
                    );
                    $memcacheConfig[] = array(
                        'host' => $host,
                        'port' => $port,
                        'persistent' => false,
                        'weight' => $weight
                    );
                }
            }
        }
        
        if ($objMemcached->addServers($parameters)) {
            return $objMemcached;
        } else {
            throw new Exception("请检查memcached服务器配置是否存在或者当前服务是否可用");
        }
    };
    
    /**
     * Setting up the redis component
     */
    $di['redis'] = function () use($config) {
        if (! empty($_SERVER['ICC_REDIS_MASTERS'])) {
            $redisServers = explode(',', $_SERVER['ICC_REDIS_MASTERS']);
            $parameters = array();
            if (is_array($redisServers) && ! empty($redisServers)) {
                foreach ($redisServers as $server) {
                    $parameters[] = 'tcp://' . trim($server);
                }
            }
            
            $options = array(
                'cluster' => 'redis'
            );
            $objRedis = new \Predis\Client($parameters, $options);
            return $objRedis;
        } else {
            throw new Exception("生产环境中尚未设定环境变量ICC_REDIS_MASTERS，请检查");
        }
    };
    
    if (! defined('IS_IN_CLI_MODE')) {
        /**
         * Registering a router
         */
        $di['router'] = function () {
            $router = new Router();
            
            $router->setDefaultModule("yungou");
            
            $router->add("/admin/weixin/:controller/:action", array(
                'module' => 'admin/weixin',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/lottery/:controller/:action", array(
                'module' => 'admin/lottery',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/system/:controller/:action", array(
                'module' => 'admin/system',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/prize/:controller/:action", array(
                'module' => 'admin/prize',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/goods/:controller/:action", array(
                'module' => 'admin/goods',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/tencent/:controller/:action", array(
                'module' => 'admin/tencent',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/mail/:controller/:action", array(
                'module' => 'admin/mail',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/message/:controller/:action", array(
                'module' => 'admin/message',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/payment/:controller/:action", array(
                'module' => 'admin/payment',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/sms/:controller/:action", array(
                'module' => 'admin/sms',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/member/:controller/:action", array(
                'module' => 'admin/member',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/points/:controller/:action", array(
                'module' => 'admin/points',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/invitation/:controller/:action", array(
                'module' => 'admin/invitation',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/store/:controller/:action", array(
                'module' => 'admin/store',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/site/:controller/:action", array(
                'module' => 'admin/site',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/post/:controller/:action", array(
                'module' => 'admin/post',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/order/:controller/:action", array(
                'module' => 'admin/order',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/article/:controller/:action", array(
                'module' => 'admin/article',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/freight/:controller/:action", array(
                'module' => 'admin/freight',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/task/:controller/:action", array(
                'module' => 'admin/task',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/vote/:controller/:action", array(
                'module' => 'admin/vote',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/weixinredpack/:controller/:action", array(
                'module' => 'admin/weixinredpack',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/:module/:controller/:action", array(
                'module' => 1,
                'controller' => 2,
                'action' => 3
            ));
            
            $router->add('/:controller/:action', array(
                'module' => 'yungou',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add('/', array(
                'module' => 'yungou',
                'controller' => 'index',
                'action' => 'index'
            ));
            return $router;
        };
        
        /**
         * The URL component is used to generate all kind of urls in the application
         */
        $di['url'] = function () {
            $url = new UrlResolver();
            $url->setBaseUri('/');
            return $url;
        };
    }
}
