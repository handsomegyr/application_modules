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
use Elasticsearch\ClientBuilder;

registerAutoloaders();

registerServices($di);

/**
 * Registers the module auto-loader
 */
function registerAutoloaders()
{
    $loader = new Loader();
    $loader->registerNamespaces(array(
        'App\Common\Controllers' => APP_PATH . 'apps/common/controllers/',
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
        // $connection = new DbAdapter(array(
        $connection = new \App\Common\Models\Base\Mysql\Pdo\DbAdapter(array(
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
        $pheanstalk = new Pheanstalk($config->pheanstalk->host, $config->pheanstalk->port);
        return $pheanstalk;
    };
    
    /**
     * Setting up the cache component
     */
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
                    'host' => $config['memcached']['host'],
                    'port' => $config['memcached']['port'],
                    'weight' => $config['memcached']['weight']
                )
            ),
            'client' => array(
                Memcached::OPT_HASH => Memcached::HASH_MD5,
                Memcached::OPT_PREFIX_KEY => $config['memcached']['prefix_key'] . '.'
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
        $_SERVER['ICC_MEMCACHED_SERVER'] = "{$config['memcached']['host']}:{$config['memcached']['port']}";
        // $_SERVER['ICC_MEMCACHED_SERVER'] = "860449636f404316.m.cnhzaliqshpub001.ocs.aliyuncs.com:11211";
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
            throw new \Exception("请检查memcached服务器配置是否存在或者当前服务是否可用");
        }
    };
    
    /**
     * Setting up the redis component
     */
    $di['redis'] = function () use($config) {
        $_SERVER['ICC_REDIS_MASTERS'] = "{$config['redis']['host']}:{$config['redis']['port']}";
        // if (! empty($_SERVER['ICC_REDIS_MASTERS'])) {
        // $redisServers = explode(',', $_SERVER['ICC_REDIS_MASTERS']);
        // $parameters = array();
        // if (is_array($redisServers) && ! empty($redisServers)) {
        // foreach ($redisServers as $server) {
        // $parameters[] = 'tcp://' . trim($server);
        // }
        // }
        
        // $options = array(
        // 'cluster' => 'redis'
        // );
        // $objRedis = new \Predis\Client($parameters, $options);
        // return $objRedis;
        // } else {
        // throw new \Exception("生产环境中尚未设定环境变量ICC_REDIS_MASTERS，请检查");
        // }
        if (! empty($_SERVER['ICC_REDIS_MASTERS'])) {
            $redisServer = explode(':', $_SERVER['ICC_REDIS_MASTERS']);
            $parameters = array(
                'host' => $redisServer[0],
                'port' => $redisServer[1],
                // 'database' => 15,
                'read_write_timeout' => 0
            );
            $options = null;
            $objRedis = new \Predis\Client($parameters, $options);
            return $objRedis;
        } else {
            throw new \Exception("生产环境中尚未设定环境变量ICC_REDIS_MASTERS，请检查");
        }
    };
    
    /**
     * Setting up the elasticsearch component
     */
    $di['elasticsearch'] = function () use($config) {
        $hosts = [
            // This is effectively equal to: "http://username:password!#$?*abc@foo.com:9200/"
            [
                'host' => $config->elasticsearch->host,
                'port' => $config->elasticsearch->port
            ]
        ];
        $client = ClientBuilder::create()->setHosts($hosts)->build();
        return $client;
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
            
            $router->add("/admin/questionnaire/:controller/:action", array(
                'module' => 'admin/questionnaire',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/weixinredpack/:controller/:action", array(
                'module' => 'admin/weixinredpack',
                'controller' => 1,
                'action' => 2
            ));
            $router->add("/admin/weixincard/:controller/:action", array(
                'module' => 'admin/weixincard',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/activity/:controller/:action", array(
                'module' => 'admin/activity',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/bargain/:controller/:action", array(
                'module' => 'admin/bargain',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/cronjob/:controller/:action", array(
                'module' => 'admin/cronjob',
                'controller' => 1,
                'action' => 2
            ));
            
            $router->add("/admin/sign/:controller/:action", array(
                'module' => 'admin/sign',
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
