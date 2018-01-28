<?php

/**
 * 
 * 直播任务
 * @author Administrator
 *
 */
class LiveTask extends \Phalcon\CLI\Task
{

    /**
     * 启动直播任务
     * ps aux | grep redis
     * redis-server /etc/redis.conf &
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php live run test
     */
    public function runAction(array $params)
    {
        // 不断插数据到队列中获取数据
        try {
            $redisKey = $params[0];
            echo ('redis key:' . $redisKey . "\n");
            // dir:/learn-php/phalcon/application_modules/apps/cronjob
            // echo ('dir:' . __DIR__ . "\n");
            
            define('DEBUG', 'on');
            define('WEBPATH', __DIR__);
            define('LIVEPATH', __DIR__);
            
            /**
             * Swoole框架自动载入器初始化
             */
            \Swoole\Loader::vendorInit();
            
            /**
             * 注册命名空间到自动载入器中
             */
            \Swoole\Loader::addNameSpace('App', __DIR__ . '/../../vendor/handsomegyr/model/lib/App');
            
            if (empty($redisKey)) {
                $config = require __DIR__ . '/live/swoole/config/config.php';
            } else {
                $config = require __DIR__ . '/live/swoole/config/config4demo.php';
            }
            $config['redis']['key'] = $redisKey;
            
            // print_r($config);
            // die('xxx');
            
            $webim = new \App\Live\Services\Swoole\Server($config);
            $webim->loadSetting(__DIR__ . "/live/swoole/config/swoole.ini"); // 加载配置文件
            
            /**
             * webim必须使用swoole扩展
             */
            $server = new \App\Live\Services\Swoole\Chat\Server($config['server']['host'], $config['server']['port']);
            $server->setConfig($config);
            $server->setProtocol($webim);
            // echo "server is running\n";
            $server->run($config['swoole']);
        } catch (\Exception $e) {
            die($e->getMessage() . "\n");
        }
    }
}