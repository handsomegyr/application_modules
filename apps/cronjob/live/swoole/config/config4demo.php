<?php
$config['server'] = array(
    // 监听的HOST
    'host' => '0.0.0.0',
    // 监听的端口
    'port' => '9503',
    // WebSocket的URL地址，供浏览器使用的
    'url' => 'ws://www.applicationmodule.com/chat/',
    // 用于Comet跨域，必须设置为html所在的URL
    'origin' => 'http://www.applicationmodule.com/client/'
);

$server_processors = isset($_SERVER['SERVER_PROCESSORS']) ? $_SERVER['SERVER_PROCESSORS'] : 1;
$config['swoole'] = array(
    'log_file' => LIVEPATH . '/live/swoole/log/swoole.log',
    'reactor_num ' => $server_processors * 1,
    'worker_num' => $server_processors * 2,
    // 不要修改这里
    'max_request' => 0,
    'task_worker_num' => $server_processors * 4,
    // 是否要作为守护进程
    'daemonize' => 0
);

$config['webim'] = array(
    // 聊天记录存储的目录
    'data_dir' => LIVEPATH . '/live/swoole/data/',
    'log_file' => LIVEPATH . '/live/swoole/log/webim.log',
    'blackword_file' => LIVEPATH . '/live/blackword.tree'
);

return $config;