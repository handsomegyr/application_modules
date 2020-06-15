<?php

/**
 * 公司cut系统
 * 需要在各个appserver机器上安装ansible,php,nginx等基础软件
 * 需要在master机器上安装ansible,php,nginx,svn,rsync等基础软件
 */
class CompanycutTask extends \Phalcon\CLI\Task
{
    // svn相关
    protected $SVNSERVER = 'svn://127.0.0.1/';
    protected $SVNROOT_R = '/var/svn/';
    protected $SVNCMD = '/usr/bin/svn ';
    protected $SVNADMCMD = '/usr/bin/svnadmin ';
    protected $SVNOPTIONS = ' --config-dir /var/svn --no-auth-cache --non-interactive --username admin --password admin ';    //这个'/var/.subversion’目录别删了哦.   
    protected $SVNSERVER_CONFIG_TEMPLATE = <<<'EOD'
[general]
anon-access = none
auth-access = write
password-db =/var/svn/repository/conf/passwd
authz-db = /var/svn/repository/conf/authz
realm = #_realm_#
# force-username-case = none

[sasl]
# use-sasl = true
# min-encryption = 0
# max-encryption = 256
EOD;

    protected $POST_COMMIT_CONFIG_TEMPLATE = "#!/bin/sh \nexport LANG=zh_CN.UTF-8\n/usr/bin/svn update #_path_# --config-dir /var/svn --no-auth-cache --non-interactive --username admin --password admin";

    // 开发相关
    // 开发环境
    protected $WWWROOT_DEV_R = '/home/webs/dev/';
    // 测试环境
    protected $WWWROOT_TEST_R = '/home/webs/demo/';
    // 正式环境
    protected $WWWROOT_PROD_R = '/home/webs/prod/';

    // nginx 相关    
    protected $NGINX_SERVER_DOMAIN = ".myweb.com";

    protected $NGINX_CONF_DEV_R = '/etc/nginx/conf.d/dev/';
    protected $NGINX_CONF_TEST_R = '/etc/nginx/conf.d/test/';
    protected $NGINX_CONF_PROD_R = '/etc/nginx/conf.d/prod/';

    protected $NGINX_ACCESSLOG_DEV_R = '/learn-php/log/nginx/dev/';
    protected $NGINX_ACCESSLOG_TEST_R = '/learn-php/log/nginx/test/';
    protected $NGINX_ACCESSLOG_PROD_R = '/learn-php/log/nginx/prod/';

    // laravel框架的nginx配置
    protected $NGINX_CONFIG_TEMPLATE = <<<'EOD'
server {
    listen       80;
    server_name  #_server_name_#;
    set $root_path '#_root_path_#';
    root $root_path;

    index index.php index.html index.htm;

    #定义本虚拟主机的访问日志
    access_log #_access_log_# main;

    #location / {
    #   root   $root_path;
    #   index  index.html index.htm;
    #}

    #error_page  404              /404.html;

    # redirect server error pages to the static page /50x.html
    #
    #error_page   500 502 503 504  /50x.html;
    #location = /50x.html {
    #    root   /usr/share/nginx/html;
    #}

    # try_files $uri $uri/ @rewrite;
    # location @rewrite {
    #         rewrite ^/(.*)$ /index.php?_url=/$1;
    # }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~* ^/(css|img|js|flv|swf|download)/(.+)$ {
        root $root_path;
    }

    # proxy the PHP scripts to Apache listening on 127.0.0.1:80
    #
    #location ~ \.php$ {
    #    proxy_pass   http://127.0.0.1;
    #}

    # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000
    #
    location ~ \.php$ {
        root           $root_path;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        try_files $uri $uri/ /index.php?$query_string;
        #fastcgi_param  SCRIPT_FILENAME  /scripts$fastcgi_script_name;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }

    # deny access to .htaccess files, if Apache's document root
    # concurs with nginx's one
    #
    #location ~ /\.ht {
    #    deny  all;
    #}
}
EOD;
    // phalcon框架的nginx配置
    protected $NGINX_CONFIG_PHALCON_TEMPLATE = <<<'EOD'
server  {
    listen       80;
    server_name #_server_name_#;
    index index.php index.html index.htm default.html default.htm default.php;
    set $root_path '#_root_path_#';
    root $root_path;
    #include other
    location / {
        # 跨域相关
        add_header "Access-Control-Allow-Origin" *;
        add_header "Access-Control-Allow-Headers" 'Origin, X-Requested-With, Content-Type, Accept';
        #index index.html index.htm index.php;        
        try_files $uri $uri/ @rewrites;      
    } 
    location @rewrites {
        rewrite ^(.*)$ /index.php?_url=$1;        
    }
    location ~ \.php {
        # 跨域相关
        add_header "Access-Control-Allow-Origin" *;
        add_header "Access-Control-Allow-Headers" 'Origin, X-Requested-With, Content-Type, Accept';        
        fastcgi_pass 127.0.0.1:9000;        
        fastcgi_index index.php;
        include fastcgi.conf;        
        include fastcgi_params;        
        fastcgi_split_path_info       ^(.+\.php)(/.+)$;        
        fastcgi_param PATH_INFO       $fastcgi_path_info;        
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;        
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;    
    }    
    location ~* ^/(css|img|js|flv|swf|download)/(.+)$ {        
        root $root_path;    
    }
    location ~ /\.ht {        
        deny all;    
    }                
    access_log  #_access_log_#  main;
}
EOD;

    // 文件同步相关
    //protected $RSYNCCMD = "/bin/env USER='root' RSYNC_PASSWORD='guotingyu0324' /usr/bin/rsync -azu --progress --delete --exclude='.svn' --exclude='.git' --exclude='.buildpath' --exclude='.project' --exclude='.gitignore' --exclude='*.log' --exclude='/logs/*' --exclude='/cache/*' --exclude=node_modules "; //--blocking-io
    protected $RSYNCCMD = "rsync -azu --delete --exclude=*.log --exclude=.svn  --exclude=*.svn ";
    protected $RSYNCSERVER = '192.168.81.129';
    protected $IPS = array('192.168.81.129');

    // HIVE
    const HIVE_BIN = '/home/hadoop/hive/bin/hive';

    // OOZIE
    const OOZIE_BIN = 'oozie';
    const OOZIE_SERVER = 'http://127.0.0.1:11000/oozie';

    /**
     * 公司CUT相关的处理
     * cd /learn-php/phalcon/application_modules/apps/cronjob
     * /usr/bin/php /learn-php/phalcon/application_modules/public/cli.php companycut handle p001 create_project
     */
    public function handleAction(array $params)
    {
        // 是否debug
        $is_debug = false;
        if (!empty($params)) {
            $project_code = $params[0];
            if (!empty($params[1])) {
                $process_list = $params[1];
            } else {
                $process_list = 'create_project';
            }
            $is_debug = true;
        }
        $do_time = time();

        // 是debug的时候
        if (!empty($is_debug)) {
            // 测试用
            $taskInfo = array();
            $taskInfo['content'] = array('process_list' => $process_list, 'project_code' => $project_code);
            $cutTaskList['datas'][] = $taskInfo;
            // 循环处理
            foreach ($cutTaskList['datas'] as $taskInfo) {
                $taskResult = $this->doTask($taskInfo, $do_time);
                print_r($taskResult);
            }
            echo "project_code:{$project_code} process_list:{$process_list} debug test";
            return;
        } else {
            // 检查是否锁定，如果没有锁定加锁
            $key = cacheKey(__FILE__, __CLASS__, __METHOD__);
            $objLock = new \iLock($key);
            if ($objLock->lock()) {
                echo "上次操作还未完成,请等待";
                return false;
            }

            $modelTask = new \App\Cronjob\Models\Task();
            // 从task表中取出cut相关的任务进行处理  
            $query = array('type' => 1, 'is_done' => false);
            $sort = array('_id' => 1, 'do_num' => -1);
            $cutTaskList = $modelTask->find($query, $sort, 0, 100);
            if (empty($cutTaskList['datas'])) {
                echo "无进行操作的任务";
                return false;
            }
            $listHandled = array();
            // 循环处理
            foreach ($cutTaskList['datas'] as $taskInfo) {
                try {
                    $modelTask->begin();
                    $taskResult = $this->doTask($taskInfo, $do_time);
                    $listHandled[] = $taskResult;
                    if (!empty($taskResult['taskSuccess'])) {
                        // 成功的话
                        $modelTask->finishTask($taskInfo, $do_time, $taskResult);
                    } else {
                        throw new \Exception(\json_encode($taskResult['errorTask']));
                    }
                    $modelTask->commit();
                } catch (\Exception $e) {
                    $modelTask->rollback();

                    // 记录处理状况
                    $memo = array();
                    $memo['error_msg'] = array(
                        'error_code' => $e->getCode(),
                        'error_msg' => $e->getMessage()
                    );
                    // 超过几次后就设置成完成
                    $is_done = false;
                    if (intval($taskInfo['do_num']) >= 0) {
                        $is_done = true;
                    }
                    $modelTask->recordTaskInfo($taskInfo, $is_done, $do_time, $memo);
                }
            }

            echo \json_encode($listHandled) . " is handled";
            return;
        }
    }

    protected function doTask($taskInfo, $do_time)
    {
        $taskResult = array();
        $taskResult['taskInfo'] = $taskInfo;
        $taskResult['taskSuccess'] = true;

        $taskContent = $taskInfo['content'];
        $project_code = empty($taskContent['project_code']) ? "" : $taskContent['project_code'];
        $project_id = empty($taskContent['project_id']) ? "" : $taskContent['project_id'];
        $db_pwd = empty($taskContent['db_pwd']) ? "" : $taskContent['db_pwd'];

        // 创建工程的时候 需要做很多的操作
        if ($taskContent['process_list'] == 'create_project') {
            $processList = array(
                'svn_create', 'svn_conf_svnserve', 'svn_conf_post_comit',
                'mkdir_www_dev', 'svn_check', 'ngxin_conf_dev',
                'ngxin_conf_test', 'ngxin_conf_prod',
                'create_db',
                'reload_nginx_dev', 'reload_nginx_all'
            );
        } else {
            $processList = explode(',', $taskContent['process_list']);
        }

        // 如果有数据的话
        if (!empty($processList)) {
            foreach ($processList as $process_name) {
                $cmdline = "";
                $output = "";
                $ret = "";
                $tip = "";
                $success = true;
                if ($process_name == 'svn_create') {
                    // svn create 版本库
                    // svnadmin create /mnt/svn/test2
                    $realm = $this->SVNROOT_R . $project_code;
                    if (!file_exists($realm . '/conf/svnserve.conf')) {
                        $subcommand = ' create ';
                        $cmdline = $this->SVNADMCMD . $subcommand . $realm  . ' 2>&1 ';
                        $tip = exec("$cmdline", $output, $ret);
                    }
                } elseif ($process_name == 'svn_import') {
                    // 将工程交由版本库控制：svn import 项目目录 SVN Repository URL -m "提交日志信息"
                    // svn import E:\SVN\workProejcts\projectA https://wangdaye-PC/svn/myRepository/ -m "initial import"
                    $workingcopy_path = $this->WWWROOT_DEV_R . $project_code;
                    $subcommand = ' import ';
                    $svn_url = $this->SVNSERVER . $project_code;
                    $cmdline = $this->SVNCMD . $subcommand . $workingcopy_path . ' ' . $svn_url . ' -m "' . $project_code . '" ' . $this->SVNOPTIONS . ' 2>&1 ';
                    $tip = exec("$cmdline", $output, $ret);
                } elseif ($process_name == 'svn_conf_svnserve') {
                    // 创建conf/svnserve.conf文件
                    $realm = $this->SVNROOT_R . $project_code;
                    if (true || !file_exists($realm . "/conf/svnserve.conf")) {
                        $config4SvnServer = str_replace("#_realm_#", $realm, $this->SVNSERVER_CONFIG_TEMPLATE);
                        $cmdline = "file_put_contents to {$realm}/conf/svnserve.conf";
                        $tip = file_put_contents($realm . "/conf/svnserve.conf", $config4SvnServer);
                    }
                } elseif ($process_name == 'svn_conf_post_comit') {
                    // 创建hooks/post-commit文件                        
                    $realm = $this->SVNROOT_R . $project_code;
                    if (true || !file_exists($realm . "/hooks/post-commit")) {
                        $workingcopy_path = $this->WWWROOT_DEV_R . $project_code;
                        $config4PostCommit = str_replace("#_path_#", $workingcopy_path, $this->POST_COMMIT_CONFIG_TEMPLATE);
                        $cmdline = "file_put_contents to {$realm}/hooks/post-commit";
                        $tip = file_put_contents($realm . "/hooks/post-commit", $config4PostCommit);
                        $tip = chmod($realm . "/hooks/post-commit", 777);
                    }
                } elseif ($process_name == 'mkdir_www_dev') {
                    // 创建开发环境的目录 
                    $workingcopy_path = $this->WWWROOT_DEV_R . $project_code;
                    $cmdline = "mkdir -p {$workingcopy_path}";
                    $tip = exec("$cmdline", $output, $ret);
                } elseif ($process_name == 'svn_check') {
                    // SVN checkout 代码到开发环境的目录
                    // svn checkout svn://127.0.0.1/xxx /mnt/www/dev/xxxx/ --username xxx --password xxx
                    $subcommand = ' checkout ';
                    $workingcopy_path = $this->WWWROOT_DEV_R . $project_code;
                    $svn_url = $this->SVNSERVER . $project_code;
                    $cmdline = $this->SVNCMD . $subcommand . $svn_url . ' ' . $workingcopy_path . $this->SVNOPTIONS . ' 2>&1 ';
                    $tip = exec("$cmdline", $output, $ret);
                } elseif ($process_name == 'ngxin_conf_dev') {
                    // 创建开发环境的nginx/conf文件
                    $server_name = $project_code . "_dev" . $this->NGINX_SERVER_DOMAIN;
                    $root_path = $this->WWWROOT_DEV_R . $project_code . "/public";
                    $access_log = $this->NGINX_ACCESSLOG_DEV_R . $project_code . ".log";
                    $file4Nginx = $this->NGINX_CONF_DEV_R . $project_code . ".conf";
                    if (true || !file_exists($file4Nginx)) {
                        $config4Nginx = $this->NGINX_CONFIG_PHALCON_TEMPLATE;
                        $config4Nginx = str_replace("#_server_name_#", $server_name, $config4Nginx);
                        $config4Nginx = str_replace("#_root_path_#", $root_path, $config4Nginx);
                        $config4Nginx = str_replace("#_access_log_#", $access_log, $config4Nginx);
                        $cmdline = "file_put_contents to {$file4Nginx}";
                        $tip = file_put_contents($file4Nginx, $config4Nginx);
                    }
                } elseif ($process_name == 'ngxin_conf_test') {
                    // 创建测试环境的nginx/conf文件
                    $server_name = $project_code . "_test" . $this->NGINX_SERVER_DOMAIN;
                    $root_path = $this->WWWROOT_TEST_R . $project_code . "/public";
                    $access_log = $this->NGINX_ACCESSLOG_TEST_R . $project_code . ".log";
                    $file4Nginx = $this->NGINX_CONF_TEST_R . $project_code . ".conf";
                    if (true || !file_exists($file4Nginx)) {
                        $config4Nginx = $this->NGINX_CONFIG_PHALCON_TEMPLATE;
                        $config4Nginx = str_replace("#_server_name_#", $server_name, $config4Nginx);
                        $config4Nginx = str_replace("#_root_path_#", $root_path, $config4Nginx);
                        $config4Nginx = str_replace("#_access_log_#", $access_log, $config4Nginx);
                        $cmdline = "file_put_contents to {$file4Nginx}";
                        $tip = file_put_contents($file4Nginx, $config4Nginx);
                    }
                } elseif ($process_name == 'ngxin_conf_prod') {
                    // 创建正式环境的nginx/conf文件
                    $server_name = $project_code . $this->NGINX_SERVER_DOMAIN;
                    $root_path = $this->WWWROOT_PROD_R . $project_code . "/public";
                    $access_log = $this->NGINX_ACCESSLOG_PROD_R . $project_code . ".log";
                    $file4Nginx = $this->NGINX_CONF_PROD_R . $project_code . ".conf";
                    if (true || !file_exists($file4Nginx)) {
                        $config4Nginx = $this->NGINX_CONFIG_PHALCON_TEMPLATE;
                        $config4Nginx = str_replace("#_server_name_#", $server_name, $config4Nginx);
                        $config4Nginx = str_replace("#_root_path_#", $root_path, $config4Nginx);
                        $config4Nginx = str_replace("#_access_log_#", $access_log, $config4Nginx);
                        $cmdline = "file_put_contents to {$file4Nginx}";
                        $tip = file_put_contents($file4Nginx, $config4Nginx);
                    }
                } elseif ($process_name == 'reload_nginx_all') {
                    // 重启所有环境的nginx服务
                    $cmdline = 'ansible storm_cluster -m command -a "systemctl reload nginx"';
                    $tip = exec("$cmdline", $output, $ret);
                } elseif ($process_name == 'reload_php_all') {
                    // 重启所有环境的php服务
                    $cmdline = 'ansible storm_cluster -m command -a "systemctl restart php-fpm"';
                    $tip = exec("$cmdline", $output, $ret);
                } elseif ($process_name == 'reload_nginx_dev') {
                    // 重启开发环境的nginx服务
                    $cmdline = "systemctl reload nginx";
                    $tip = exec("$cmdline", $output, $ret);
                } elseif ($process_name == 'rsync_dev_to_test') {
                    // 将开发环境的代码同步到测试环境
                    // 先将开发用的代码目录同步到测试用的代码目录                      
                    // "rsync -azu --delete --exclude=*.log --exclude=.svn  --exclude=*.svn /mnt/www/"+project_code+"_demo/ root@"+ip[i]+":/mnt/www/"+project_code+"_demo/"
                    $cmdline = $this->RSYNCCMD . " " . $this->WWWROOT_DEV_R . $project_code . "/" . " " . $this->WWWROOT_TEST_R . $project_code . "/";
                    $tip = exec("$cmdline", $output, $ret);

                    // 从本地机器上执行rsync命令同步到各个app服务器上
                    foreach ($this->IPS as $ip) {
                        $cmdline = $this->RSYNCCMD . " " . $this->WWWROOT_TEST_R . $project_code . "/" . " root@{$ip}:" . $this->WWWROOT_TEST_R . $project_code . "/";
                        $tip = exec("$cmdline", $output, $ret);
                    }

                    // 或则用ansible工具处理
                } elseif ($process_name == 'publish_test_to_prod') {
                    // 将测试环境的代码发布到正式环境 
                    // 先将测试用的代码目录同步到正式用的代码目录
                    // "rsync -azu --progress --delete --exclude=*.log --exclude=*.svn --exclude=.svn /mnt/www/"+project_code+"_demo"+" /mnt/www/"+project_code,
                    $cmdline = $this->RSYNCCMD . " " . $this->WWWROOT_TEST_R . $project_code . "/" . " " . $this->WWWROOT_PROD_R . $project_code . "/";
                    $tip = exec("$cmdline", $output, $ret);

                    // 从本地机器上执行rsync命令同步到各个app服务器上
                    foreach ($this->IPS as $ip) {
                        $cmdline = $this->RSYNCCMD . " " . $this->WWWROOT_PROD_R . $project_code . "/" . " root@{$ip}:" . $this->WWWROOT_PROD_R . $project_code . "/";
                        $tip = exec("$cmdline", $output, $ret);
                    }
                    // 或则用ansible工具处理

                } elseif ($process_name == 'rsync_server_to_test') {
                    // 将开发环境nginx配置同步到各个测试环境 
                    $cmdline = 'ansible storm_cluster -m command -a "' . $this->RSYNCCMD . " " . $this->NGINX_CONF_TEST_R . $project_code . ".conf" . " " . $this->NGINX_CONF_TEST_R . $project_code . ".conf" . '"';
                    $tip = exec("$cmdline", $output, $ret);
                } elseif ($process_name == 'rsync_server_to_prod') {
                    // 将开发环境nginx配置同步到各个正式环境 
                    $cmdline = 'ansible storm_cluster -m command -a "' . $this->RSYNCCMD . " " . $this->NGINX_CONF_PROD_R . $project_code . ".conf" . " " . $this->NGINX_CONF_PROD_R . $project_code . ".conf" . '"';
                    $tip = exec("$cmdline", $output, $ret);
                } elseif ($process_name == 'create_db') {
                    // 创建数据库和基础表
                    // $di = $this->getDI();
                    $di = \Phalcon\DI::getDefault();
                    $config = $di->get('config');
                    // 创建项目对应的数据库
                    $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                        "host" => $config->database->host,
                        "username" => $config->database->username,
                        "password" => $config->database->password,
                        "dbname" => "mysql",
                        "charset" => $config->database->charset,
                        "collation" => $config->database->collation,
                        'options'  => [
                            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config->database->charset} COLLATE {$config->database->collation};",
                            //\PDO::ATTR_CASE => PDO::CASE_LOWER,
                        ],
                    ));
                    $dbret1 = $connection->execute("CREATE DATABASE IF NOT EXISTS `{$project_code}` DEFAULT CHARACTER SET {$config->database->charset} COLLATE {$config->database->collation} ", array());
                    // 如果是失败的话
                    if (empty($dbret1)) {
                    }

                    // 检查用户是否存在
                    $dbUserInfo = $connection->fetchOne("SELECT * FROM user where User='{$project_code}' and Host='%'", \Phalcon\Db::FETCH_ASSOC);
                    // print_r($typeInfo);
                    if (empty($dbUserInfo)) {
                        // 创建数据库的用户
                        $dbret2 = $connection->execute("CREATE USER '{$project_code}'@'%' IDENTIFIED BY '{$db_pwd}'", array());
                        // 如果是失败的话
                        if (empty($dbret2)) {
                        }
                        // 授权数据库的用户
                        $dbret3 = $connection->execute("GRANT ALL PRIVILEGES ON {$project_code}.* TO '{$project_code}'@'%' IDENTIFIED BY '{$db_pwd}' WITH GRANT OPTION", array());
                        // 如果是失败的话
                        if (empty($dbret3)) {
                        };
                        // 刷新
                        $connection->execute("FLUSH PRIVILEGES");
                    }

                    // 创建一些基础表
                    $dir = APP_PATH . 'apps/backend/submodules/system';
                    $sqlfile = $dir . '/config/install/schema.sql';
                    if (file_exists($sqlfile)) {
                        $sql = file_get_contents($sqlfile);
                        $connection1 = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                            "host" => $config->database->host,
                            "username" => $config->database->username,
                            "password" => $config->database->password,
                            "dbname" => $project_code,
                            "charset" => $config->database->charset,
                            "collation" => $config->database->collation,
                            'options'  => [
                                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config->database->charset} COLLATE {$config->database->collation};",
                                //\PDO::ATTR_CASE => PDO::CASE_LOWER,
                            ],
                        ));
                        $sqlRet = $this->doSql($connection1, $sql);
                        // $tip = \json_encode($sqlRet);
                    }
                } elseif ($process_name == 'data_export') {
                    // 表数据导出
                    $this->doDataExport($taskContent);
                } elseif ($process_name == 'send_email') {
                    // 发送邮件
                    $workload = $taskContent['workload'];
                    $params = unserialize($workload);
                    $toEmail = $params['toEmail'];
                    $subject = $params['subject'];
                    $content = $params['content'];
                    $this->sendEmailPhpMailer($toEmail, $subject, $content);
                } elseif ($process_name == 'collection_bson_export') {
                    // 表数据bson导出
                    $this->doCollectionBsonExport($taskContent);
                } elseif ($process_name == 'create_index') {
                    // 创建索引
                    $this->doCreateIndex($taskContent);
                } elseif ($process_name == 'bson_import') {
                    // bson，csv导入
                    $this->doBsonImport($taskContent);
                } elseif ($process_name == 'bson_export') {
                    // bson，csv导出
                    $this->doBsonExport($taskContent);
                } elseif ($process_name == 'touch_data') {
                    // 数据预热
                    $this->doBsonExport($taskContent);
                } elseif ($process_name == 'drop_datas') {
                    // 数据清空
                    $this->doDropDatas($taskContent);
                } elseif ($process_name == 'create_hive_table') {
                    // 在hive中创建数据集合
                    $this->doCreateHiveTable($taskContent);
                } elseif ($process_name == 'execute_hive_sql') {
                    // 在hive中执行hivesql
                    $this->doExecuteHiveSQL($taskContent);
                } elseif ($process_name == 'oozie_work_flow') {
                    // 执行Oozie的工作流
                    $this->doOozieWorkflow($taskContent);
                } elseif ($process_name == 'delete_removed_data') {
                    // 删除被removed的数据
                    $this->doDeleteRemovedData($taskContent);
                } elseif ($process_name == 'log_error') {
                    // 记录错误信息
                    $this->doLogError($taskContent);
                } elseif ($process_name == 'plugin_collection_sync') {
                    // 同步插件集合数据结构
                    $this->doPluginCollectionSync($taskContent);
                } elseif ($process_name == 'sync_collection_in_single_plugin') {
                    // 同步在特定插件中的集合
                    $this->doSyncCollectionInSinglePlugin($taskContent);
                } elseif ($process_name == 'plugin_index_sync') {
                    // 同步插件索引
                    $this->doPluginIndexSync($taskContent);
                }

                // 成功的话
                if (empty($ret)) {
                    $success = true;
                } else {
                    $success = false;
                }
                $result_ary = array();
                $result_ary['process_name'] = $process_name;
                $result_ary['project_code'] = $project_code;
                $result_ary['project_id'] = $project_id;
                $result_ary['success'] = $success;
                $result_ary['cmdline'] = $cmdline;
                $result_ary['tip'] = $tip;
                $result_ary['ret'] = $ret;
                $result_ary['output'] = $output;
                $taskResult['processList'][] = $result_ary;
                $taskResult['taskSuccess'] = $taskResult['taskSuccess'] && $success;
                if (!$success) {
                    $taskResult['taskErrorMsg'] = $tip;
                    $taskResult['errorTask'] = $result_ary;
                    return $taskResult;
                }
            }
        }

        return $taskResult;
    }

    // 执行sql命令
    protected function doSql($connection, $sql)
    {
        $sql = str_replace("\r\n", "\n", $sql);
        $sql = str_replace("\r", "\n", $sql);

        $num = 0;
        foreach (explode(";\n", trim($sql)) as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            foreach ($queries as $query) {
                // $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0] . $query[1] == '--') ? '' : $query;
                $ret[$num] .= $query;
            }
            $num++;
        }
        // return $ret;
        foreach ($ret as $query) {
            $connection->execute($query);
        }
    }

    // 表数据导出
    protected function doDataExport($taskContent)
    {
        $workload = $taskContent['workload'];
        $exportKey = $taskContent['exportKey'];
        $exportGearmanKey = $taskContent['exportGearmanKey'];

        $scriptStarTime = microtime(true);
        $params = unserialize($workload);
        $scope = $params['scope'];
        $collection_id = $params['collection_id'];
        $query = $params['query'];
        $fields = $params['fields'];

        echo "====================";
        echo date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";

        print_r($params);

        $di = \Phalcon\DI::getDefault();
        $cache = $di->get("cache"); //$this->getDI()->get("cache");

        // 获取映射关系，初始化数据集合model
        $dataModel = $this->getData4Collection($collection_id);

        // 增加a.b类型字段的支持
        $queryFields = $fields;
        foreach ($queryFields as $k => $v) {
            if (strpos($k, '.') !== false) {
                $tmp = explode('.', $k);
                $queryFields[$tmp[0]] = true;
            }
        }

        // $dataModel->setReadPreference(MONGODB_READ_PREFERENCE);
        $start = 0;
        $limit = 1000000;
        $sort = array('_id' => 1);
        $list = $dataModel->find($query, $sort, $start, $limit, $queryFields);
        $dataTotal = $list['total'];

        if ($dataTotal > $limit) {
            var_dump('$dataTotal > ' . $limit . ' stop');
            $cache->delete($exportGearmanKey);
            $fileInfo = array();
            $fileInfo['outType'] = 'toolarge';
            $cache->save($exportKey, $fileInfo, 7200);
            return false;
        }

        $excelDatas = array();
        // 保持拥有全部的字段名，不存在错乱的想象

        $loop = function ($value, $tmp) {
            $new = $value;
            $len = count($tmp);
            for ($i = 0; $i < $len; $i++) {
                if (isset($new[$tmp[$i]])) {
                    $new = $new[$tmp[$i]];
                } else {
                    return '';
                }
            }
            return $new;
        };

        $fieldNames = array_keys($fields);

        if (!empty($list['datas'])) {
            foreach ($list['datas'] as $row) {
                $tmp = array();
                foreach ($fieldNames as $key) {
                    if (strpos($key, '.') !== false) {
                        $explodeKey = explode('.', $key);
                        $tmp[$key] = $loop($row, $explodeKey);
                    } else {
                        $tmp[$key] = isset($row[$key]) ? $row[$key] : '';
                    }
                }
                $excelDatas[] = $tmp;
                unset($tmp);
            }
        }
        unset($list);

        echo "get data from database:" . (microtime(true) - $scriptStarTime);
        echo "\n";

        // 在导出数据的情况下，将关联数据显示为关联集合的显示字段数据
        $rshData = array();
        foreach ($scope->_rshCollection as $_id => $detail) {
            $model = $this->getData4Collection($_id);
            $rshDataList = $model->findAll(array(), array(
                $detail['rshCollectionKeyField'] => true,
                $detail['rshCollectionValueField'] => true
            ));

            $datas = array();
            foreach ($rshDataList as $row) {
                $key = $row[$detail['rshCollectionValueField']];
                $value = isset($row[$detail['rshCollectionKeyField']]) ? $row[$detail['rshCollectionKeyField']] : '';
                if ($key instanceof \MongoId) {
                    $key = $key->__toString();
                }
                if (!empty($key)) {
                    $datas[$key] = $value;
                }
            }

            if (is_array($detail['collectionField'])) {
                foreach ($detail['collectionField'] as $detailCollectionField) {
                    $rshData[$detailCollectionField] = $datas;
                }
            } else {
                $rshData[$detail['collectionField']] = $datas;
            }
        }

        echo "get rsh collection data from database:" . (microtime(true) - $scriptStarTime);
        echo "\n";

        echo "excelDatas";
        print_r($excelDatas);
        echo "\n";

        // 结束
        $excelDatas = convertToPureArray($excelDatas);

        echo "convertToPureArray:" . (microtime(true) - $scriptStarTime);
        echo "\n";

        array_walk($excelDatas, function (&$value, $key) use ($rshData, $fields) {
            ksort($value);
            array_walk($value, function (&$cell, $field) use ($rshData) {
                if (is_string($field) || is_int($field)) {
                    if (isset($rshData[$field])) {
                        $cell = isset($rshData[$field][$cell]) ? $rshData[$field][$cell] : '';
                    } else {
                        if (is_string($cell)) {
                            if (strtotime($cell) === false)
                                $cell = preg_replace("/\r|\n|\t|\s/", "", htmlspecialchars($cell));
                        } else {
                            if (is_array($cell)) {
                                $cell = json_encode($cell, JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }
                }
            });
        });

        echo "array_walk excelDatas:" . (microtime(true) - $scriptStarTime);
        echo "\n";

        $title = array();
        ksort($fields);
        foreach (array_keys($fields) as $field) {
            $title[] = isset($scope->_title[$field]) ? $scope->_title[$field] : $field;
        }

        $excel = array(
            'title' => $title,
            'result' => $excelDatas
        );

        $temp = '/home/webs/nas/gearman_export_' . \uniqid(); // tempnam(sys_get_temp_dir(), 'gearman_export_');

        echo "write to temp file start:" . (microtime(true) - $scriptStarTime);
        echo "\n";

        $this->arrayToCVS2($excel, null, $temp);
        $outType = 'csv';

        echo "write to temp file end:" . (microtime(true) - $scriptStarTime);
        echo "\n";

        $fileInfo = array();
        $fileInfo['_id'] = $temp;

        var_dump($fileInfo);
        echo "write to gridfs:" . (microtime(true) - $scriptStarTime);
        echo "\n";

        $fileInfo['outType'] = $outType;
        var_dump($fileInfo);

        var_dump(file_get_contents($fileInfo['_id']));

        // 确保数据同步能正确完成，当且仅当读取从节点时有效
        sleep(3);
        $cache->save($exportKey, $fileInfo, 7200);
        sleep(3);
        $cache->delete($exportGearmanKey);

        echo "====================end======================";
        echo "\n";
        return true;
    }

    /**
     * 发送邮件byPHPmailer
     *
     * @param mixed $to
     *            (array|string)
     * @param string $subject            
     * @param string $content            
     */
    protected function sendEmailPhpMailer($to, $subject, $content, $debug = 0)
    {
        $mail = new \PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->SMTPAutoTLS = false;
        $mail->SMTPDebug = $debug; // Enable verbose debug output
        $mail->isSMTP();
        $mail->Host = 'smtp.jizigou.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@jizigou.com';
        $mail->Password = '123qawq123S';
        $mail->Port = 25;

        $mail->setFrom('handsomegyr@126.com', 'System Monitor');
        if (is_array($to)) {
            foreach ($to as $email) {
                $mail->addAddress($email); // Add a recipient
            }
        } else {
            $mail->addAddress($to);
        }
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $content;

        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } else {
            return true;
        }
    }

    // 用bson格式导出表数据
    protected function doCollectionBsonExport($taskContent)
    {
        $workload = $taskContent['workload'];
        $params = unserialize($workload);

        $key = $params['key'];
        $collection_id = $params['collection_id'];
        $dataModel = $this->getData4Collection($collection_id);
        $collection_name = $dataModel->getSource();

        $zipName = 'bson_' . \uniqid() . '.zip';
        $tmp = '/home/webs/nas/' . $zipName; // tempnam(sys_get_temp_dir(), 'zip_');
        var_dump($tmp);

        $zip = new \ZipArchive();
        $res = $zip->open($tmp, \ZipArchive::CREATE);
        if ($res === true) {
            // 添加项目数据
            $filename = $this->collection2bson($dataModel, array());
            var_dump($filename);
            var_dump(filesize($filename));
            var_dump($zip->addFile($filename, $collection_name . '.bson'));
        }
        $zip->close();

        if (!empty($filename))
            unlink($filename);

        // 存入mongodb中用于中间读取
        $fileInfo = array();
        $fileInfo['_id'] = $tmp;
        $fileInfo['filename'] = $zipName;
        // $fileInfo['mime'] = 'application/zip';
        $fileInfo['size'] = $fileInfo['length'] = filesize($tmp);
        var_dump($fileInfo);

        $di = \Phalcon\DI::getDefault();
        $cache = $di->get("cache");
        $cache->save($key, $fileInfo, 60);
        return true;
    }

    // 创建索引
    protected function doCreateIndex($taskContent)
    {
        $workload = $taskContent['workload'];
        $params = unserialize($workload);

        echo "====================";
        echo date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";

        $index_name = isset($params['index_name']) ? $params['index_name'] : '';
        $collection_id = isset($params['collection_id']) ? $params['collection_id'] : '';
        $indexInfo = isset($params['keys']) ? $params['keys'] : '';
        $options = isset($params['options']) ? $params['options'] : array();
        // 创建索引
        if (!empty($collection_id) && !empty($index_name) && !empty($indexInfo) && is_array($indexInfo)) {

            $modelCompanyProject = new \App\Company\Models\Project();
            $modelProjectCollection = new \App\Database\Models\Project\Collection();

            // 获取表信息数据
            $dataCollectionInfo = $modelProjectCollection->getInfoById($collection_id);
            if (empty($dataCollectionInfo)) {
                throw new \Exception("该collection_id:{$collection_id}所对应的集合不存在");
            }
            $companyProjectInfo = $modelCompanyProject->getInfoById($dataCollectionInfo['company_project_id']);
            if (empty($companyProjectInfo)) {
                throw new \Exception('集合的company_project_id无效');
            }
            $objDatabaseManager = new \DatabaseManager($companyProjectInfo['project_code'], $companyProjectInfo['project_code'], $companyProjectInfo['db_pwd']);

            // 创建表索引
            $ret = $objDatabaseManager->addIndex($dataCollectionInfo['alias'], $index_name, $indexInfo, $options);
            var_dump($ret);
        }

        echo "====================end======================";
        echo "\n";
        return true;
    }

    // Bson数据导入
    protected function doBsonImport($taskContent)
    {
        $workload = $taskContent['workload'];
        $params = unserialize($workload);
        $key = $params['key'];
        $admin_id = $workload['admin_id'];
        $admin_name = $workload['admin_name'];
        $collection_id = $params['collection_id'];
        $physicalDrop = $params['physicalDrop'];

        $modelCompanyProject = new \App\Company\Models\Project();
        $modelProjectCollection = new \App\Database\Models\Project\Collection();
        $modelProjectCollectionStructure = new \App\Database\Models\Project\Collection\Structure();

        // 获取表信息数据
        $dataCollectionInfo = $modelProjectCollection->getInfoById($collection_id);
        if (empty($dataCollectionInfo)) {
            throw new \Exception("该collection_id:{$collection_id}所对应的集合不存在");
        }
        $companyProjectInfo = $modelCompanyProject->getInfoById($dataCollectionInfo['company_project_id']);
        if (empty($companyProjectInfo)) {
            throw new \Exception('集合的company_project_id无效');
        }

        // 获取集合的数据结构
        $_schema = array();
        $_fields = array();
        $list = $modelProjectCollectionStructure->findAll(array(
            'company_project_id' => $dataCollectionInfo['company_project_id'],
            'project_id' => $dataCollectionInfo['project_id'],
            'collection_id' => $collection_id
        ));
        foreach ($list as $row) {
            $_schema[$row['label']] = $row['field'];
            $_fields[$row['field']] = $row['type'];
        }

        $objDatabaseManager = new \DatabaseManager($companyProjectInfo['project_code'], $companyProjectInfo['project_code'], $companyProjectInfo['db_pwd']);

        // 如果是物理删除数据的话
        if ($physicalDrop) {
            // 重新建表操作2  保留表结构和索引
            $tablename = $dataCollectionInfo['alias'];
            $new_tablename = $tablename . "_del_" . date('YmdHis') . "_" . \uniqid();

            // 先改表名
            $objDatabaseManager->alterTableName($tablename, $new_tablename);

            // 再生成一个相同表名称的表
            $objDatabaseManager->cloneTable($tablename, $new_tablename);
        }

        // 加载csv数据
        $csv = file_get_contents($key);

        // 判断该文件是不是压缩文件,如果是压缩文件解压处理，注意只能解压压缩包根目录下的文件
        if (isZip($csv)) {
            $tmpZip = tempnam(sys_get_temp_dir(), 'tmpzip_');
            file_put_contents($tmpZip, $csv);
            $unzipList = unzip($tmpZip, array(
                'csv'
            ));
            if (empty($unzipList)) {
                throw new Exception("加压文件列表为空，未发现有效的csv文件");
            }
            $csv = file_get_contents($unzipList[0]);
            unlink($unzipList[0]);
            unlink($tmpZip);
        }

        // 删除过期的临时缓存文件
        unlink($key);

        if (empty($csv)) {
            echo '$csv is empty';
            return false;
        }

        $arr = csv2arr($csv);
        unset($csv); // 释放内存

        if (empty($arr)) {
            echo '$arr is empty';
            return false;
        }

        // 获取第一行的数据
        $firstRow = array_shift($arr);
        var_dump(__LINE__, $firstRow);

        $titles = array();
        foreach ($firstRow as $col => $value) {
            $value = trim($value);
            var_dump(__LINE__, $col, $value);
            var_dump(__LINE__, $value, array_keys($_schema));
            if (in_array($value, array_keys($_schema), true)) {
                $titles[$col] = $_schema[$value];
                var_dump(__LINE__, $value, $_schema[$value]);
            } else {
                var_dump(__LINE__, $value, array_values($_schema));
                if (in_array($value, array_values($_schema), true)) {
                    $titles[$col] = $value;
                }
            }
            var_dump(__LINE__, $titles);
        }
        var_dump(__LINE__, $_schema);
        var_dump(__LINE__, $titles);
        if (count($titles) == 0) {
            var_dump(__LINE__, $titles);
            var_dump(__LINE__, $firstRow);
            echo '无匹配的标题或者标题字段，请检查导入数据的格式是否正确';
            return false;
        }

        $now = date("Y-m-d H:i:s", time());
        $temp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bson_' . \uniqid() . '.csv';
        $fp = fopen($temp, 'w');
        foreach ($arr as $row) {
            $insertData = array();
            foreach ($titles as $col => $colName) {
                $insertData[$colName] = $row[$col];
            }
            $id = new \MongoId();
            $insertData['_id'] = $id->__toString();
            $insertData['__REMOVED__'] = 0;
            $insertData['__CREATE_TIME__'] = $now;
            $insertData['__CREATE_USER_ID__'] = $admin_id;
            $insertData['__CREATE_USER_NAME__'] = $admin_name;

            $insertData['__MODIFY_TIME__'] = $now;
            $insertData['__MODIFY_USER_ID__'] = $admin_id;
            $insertData['__MODIFY_USER_NAME__'] = $admin_name;

            // fwrite($fp, bson_encode($insertData));
            fputcsv($fp, $insertData);
        }
        fclose($fp);

        // 执行导入脚本
        $objDatabaseManager->importCsvData($tablename, $temp);
        echo "\n";
        unlink($temp);

        echo "\ncomplete";
        return true;
    }

    // Bson数据导出
    protected function doBsonExport($taskContent)
    {
        $workload = $taskContent['workload'];
        $params = unserialize($workload);

        $key = $params['key'];
        $project_id = $params['project_id'];
        $company_project_id = $params['company_project_id'];

        var_dump("=======bson获取开始:" . date("Y-m-d H:i:s") . "=============\n");

        $zipName = 'bson_' . \uniqid() . '.zip';
        $tmp = '/home/webs/nas/' . $zipName; // tempnam(sys_get_temp_dir(), 'zip_');
        var_dump($tmp);

        $zip = new \ZipArchive();
        $res = $zip->open($tmp, \ZipArchive::CREATE);
        if ($res === true) {
            // 添加项目数据
            $modelProject = new \App\Database\Models\Project();
            $filename = $this->collection2bson($modelProject, array(
                '_id' => $project_id
            ));
            $zip->addFile($filename, 'idatabase_project' . '.csv');
            // unlink($filename);

            // 获取密钥信息
            $modelProjectSn = new \App\Database\Models\Project\Sn();
            $filename = $this->collection2bson($modelProjectSn, array(
                'company_project_id' => $company_project_id,
                'project_id' => $project_id
            ));
            $zip->addFile($filename, 'idatabase_project_sn' . '.csv');
            // unlink($filename);

            // 添加集合数据
            $modelProjectCollection = new \App\Database\Models\Project\Collection();
            $filename = $this->collection2bson($modelProjectCollection, array(
                'company_project_id' => $company_project_id,
                'project_id' => $project_id
            ));
            $zip->addFile($filename, 'idatabase_project_collection' . '.csv');
            // unlink($filename);

            // 添加结构数据
            $modelProjectCollectionStructure = new \App\Database\Models\Project\Collection\Structure();
            $filename = $this->collection2bson($modelProjectCollectionStructure, array(
                'company_project_id' => $company_project_id,
                'project_id' => $project_id
            ));
            $zip->addFile($filename, 'idatabase_project_collection_structure' . '.csv');
            // unlink($filename);

            // 获取映射信息
            $modelProjectCollectionMapping = new \App\Database\Models\Project\Collection\Mapping();
            $filename = $this->collection2bson($modelProjectCollectionMapping, array(
                'company_project_id' => $company_project_id,
                'project_id' => $project_id
            ));
            $zip->addFile($filename, 'idatabase_project_collection_mapping' . '.csv');
            // unlink($filename);

            // 导出集合数据信息
            $list4Collection = $modelProjectCollection->findAll(array(
                'company_project_id' => $company_project_id,
                'project_id' => $project_id
            ));

            if (!empty($list4Collection)) {

                // 添加集合列表映射关系说明
                $filename = $this->projectMap2Excel($list4Collection);
                $zip->addFile($filename, '项目集合列表.csv');
                // unlink($filename);

                foreach ($list4Collection as $collectionInfo) {
                    $collection_id = $collectionInfo['_id'];
                    $collection_name = $collectionInfo['alias'];

                    $dataModel = $this->getData4Collection($collection_id);
                    $filename = $this->collection2bson($dataModel, array());
                    $zip->addFile($filename, $collection_name . '.csv');
                    // unlink($filename);

                    // 添加文档结构说明
                    $filename = $this->collectionStructure2Excel($collectionInfo);
                    $zip->addFile($filename, $collection_name . '文档结构说明.xlsx');
                    // unlink($filename);
                }
            }
        }
        $zip->close();

        // 存入mongodb中用于中间读取
        $fileInfo = array();
        $fileInfo['_id'] = $tmp;
        $fileInfo['filename'] = $zipName;
        // $fileInfo['mime'] = 'application/zip';
        $fileInfo['size'] = $fileInfo['length'] = filesize($tmp);
        var_dump($fileInfo);

        $di = \Phalcon\DI::getDefault();
        $cache = $di->get("cache");
        $cache->save($key, $fileInfo, 60);
        return true;
    }

    // 数据预热
    protected function doTouchData($taskContent)
    {
        $workload = $taskContent['workload'];
        $params = unserialize($workload);
        $project_id = $params['project_id'];
        $company_project_id = $params['company_project_id'];
        var_dump($params);

        $modelProjectCollection = new \App\Database\Models\Project\Collection();
        $list4Collection = $modelProjectCollection->findAll(array(
            'company_project_id' => $company_project_id,
            'project_id' => $project_id
        ));

        if (empty($list4Collection)) {
            return false;
        }

        foreach ($list4Collection as $row) {

            $collection_id = $row['_id'];
            var_dump($collection_id);

            $modelData = $this->getData4Collection($collection_id);
            $cursorData = $modelData->find(array(), array('_id' => -1), 0, 10000);
            var_dump($collection_id . 'preload is ok');
        }
        return true;
    }

    // 数据清空
    protected function doDropDatas($taskContent)
    {
        $workload = $taskContent['workload'];
        $params = unserialize($workload);
        $collection_id = $params['collection_id'];

        echo "====================";
        echo date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";

        $modelCompanyProject = new \App\Company\Models\Project();
        $modelProjectCollection = new \App\Database\Models\Project\Collection();

        // 获取表信息数据
        $dataCollectionInfo = $modelProjectCollection->getInfoById($collection_id);
        if (empty($dataCollectionInfo)) {
            throw new \Exception("该collection_id:{$collection_id}所对应的集合不存在");
        }
        $companyProjectInfo = $modelCompanyProject->getInfoById($dataCollectionInfo['company_project_id']);
        if (empty($companyProjectInfo)) {
            throw new \Exception('集合的company_project_id无效');
        }

        $objDatabaseManager = new \DatabaseManager($companyProjectInfo['project_code'], $companyProjectInfo['project_code'], $companyProjectInfo['db_pwd']);

        // 重新建表操作2  保留表结构和索引
        $tablename = $dataCollectionInfo['alias'];
        $new_tablename = $tablename . "_del_" . date('YmdHis') . "_" . \uniqid();

        // 先改表名
        $objDatabaseManager->alterTableName($tablename, $new_tablename);

        // 再生成一个相同表名称的表
        $objDatabaseManager->cloneTable($tablename, $new_tablename);

        echo "====================end======================";
        echo "\n";
        return true;
    }

    // 在hive中创建数据集合
    protected function doCreateHiveTable($taskContent)
    {
        $workload = $taskContent['workload'];
        $params = unserialize($workload);

        echo "\n";
        echo $sql = $params['hiveSQL'];
        echo "\n";

        echo "====================";
        echo date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";
        echo $sql;
        echo "\n";
        echo $fileName = toTemp($sql);
        echo "\n";
        echo $cmd = self::HIVE_BIN . " -f \"{$fileName}\"";
        echo "\n";
        runCmd($cmd, true);
        unlink($fileName);
        echo "====================end======================";
        echo "\n";
        return true;
    }

    // 在hive中执行hivesql
    protected function doExecuteHiveSQL($taskContent)
    {
        $workload = $taskContent['workload'];
        $params = unserialize($workload);

        echo $result_collection = $params['result_collection'];
        // $result_collection = preg_replace('/idatabase_collection_/i', '', $result_collection);

        echo $params['hiveSQL'];
        if (preg_match('/insert/i', $params['hiveSQL'])) {
            echo "hiveSQL包含有风险的语句，比如insert\n";
            return false;
        }

        // 检测结合是否为统计结果集合
        $modelProjectCollection = new \App\Database\Models\Project\Collection();
        $check = $modelProjectCollection->findOne(array(
            '_id' => ($result_collection),
            'is_hive_result_collection' => true
        ));
        if (empty($check)) {
            echo "目标集合非统计结果集合，不能写入数据\n";
            return false;
        }

        $sql = 'INSERT OVERWRITE TABLE ' . $check['alias'];
        echo $sql .= $params['hiveSQL'];

        echo "\n";
        echo "====================";
        echo date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";
        echo $fileName = toTemp($sql);
        echo "\n";
        echo $cmd = self::HIVE_BIN . " -f \"{$fileName}\"";
        echo "\n";
        runCmd($cmd, true);
        unlink($fileName);
        echo "====================end======================";
        echo "\n";
        return true;
    }

    // 执行Oozie的工作流
    protected function doOozieWorkflow($taskContent)
    {
        $workload = $taskContent['workload'];
        $params = unserialize($workload);
        echo $sql = $params['hiveSQL'];

        echo "\n";
        echo "====================";
        echo date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";
        echo $fileName = toTemp($sql);
        echo "\n";
        echo $cmd = self::OOZIE_BIN . " oozie job -oozie " . self::OOZIE_SERVER . " -config examples/apps/map-reduce/job.properties -run";
        echo "\n";
        runCmd($cmd, true);
        unlink($fileName);
        echo "====================end======================";
        echo "\n";
        return true;
    }

    // 删除被removed的数据
    protected function doDeleteRemovedData($taskContent)
    {
        $workload = $taskContent['workload'];
        $params = unserialize($workload);
        var_dump($params);
        $collection_id = $params['collection_id'];

        echo "\n";
        echo "====================";
        echo date("Y-m-d H:i:s");
        echo "====================";

        $dataModel = $this->getData4Collection($collection_id);
        $dataModel->physicalRemove(array(
            '__REMOVED__' => true
        ));

        echo "====================end======================";
        echo "\n";

        return true;
    }

    // 同步插件集合数据结构
    protected function doPluginCollectionSync($taskContent)
    {
        $workload = $taskContent['workload'];
        echo "====================start_plugin_sync";
        echo date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";

        $key = md5($workload);
        $params = unserialize($workload);
        $project_id = $params['project_id'];
        $plugin_id = $params['plugin_id'];

        var_dump(__LINE__, $params);
        echo "\n";

        $datas = array();
        $cursor = $this->_plugin_collection->find(array(
            'plugin_id' => $plugin_id
        ));
        if ($cursor->count() > 0) {
            while ($cursor->hasNext()) {
                $row = $cursor->getNext();
                // $this->_plugin_collection->syncPluginCollection($project_id, $plugin_id, $row['alias']);
                // 此处此前是单个集合逐个进行同步执行调整为并发执行
                $params = array();
                $params['project_id'] = $project_id;
                $params['plugin_id'] = $plugin_id;
                $params['collectionName'] = $row['alias'];
                $key = md5(serialize($params));
                $this->_gmClient->doBackground('syncCollectionInSinglePlugin', serialize($params), $key);
            }
            return true;
        } else {
            var_dump(__LINE__, '程序异常：未查询到有效的记录');
            echo "\n";
            return false;
        }
    }

    // 同步在特定插件中的集合
    protected function doSyncCollectionInSinglePlugin($taskContent)
    {
        $workload = $taskContent['workload'];
        echo "====================syncCollectionInSinglePlugin sync start";
        echo date("Y-m-d H:i:s");
        echo "====================";
        echo "\n";

        $key = md5($workload);
        $params = unserialize($workload);
        $project_id = $params['project_id'];
        $plugin_id = $params['plugin_id'];
        $collectionName = $params['collectionName'];

        var_dump(__LINE__, $params);
        echo "\n";

        $this->_plugin_collection->syncPluginCollection($project_id, $plugin_id, $collectionName);

        echo "====================syncCollectionInSinglePlugin sync end";
        echo date("Y-m-d H:i:s");
        echo "====================";
        echo "\n\n";
        return true;
    }

    // 同步插件索引
    protected function doPluginIndexSync($taskContent)
    {
        $workload = $taskContent['workload'];
        $key = md5($workload);
        $params = unserialize($workload);
        $project_id = $params['project_id'];
        $plugin_id = $params['plugin_id'];
        var_dump($params);

        $objPluginIndex = $this->model('Idatabase\Model\PluginIndex');
        // $objPluginIndex->setReadPreference(MONGODB_READ_PREFERENCE);

        $rst = $objPluginIndex->autoCreateIndexes($project_id, $plugin_id);
        return true;
    }

    //记录错误信息
    protected function doLogError($taskContent)
    {
        $msg = $taskContent['workload'];

        if (!is_string($msg)) {
            $msg = json_encode($msg);
        }

        $msg = join("\t", array(
            date("Y-m-d H:i:s"),
            isset($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : 'cli',
            $msg,
            PHP_EOL
        ));
        $fp = fopen('/home/webs/nas/logs/' . date("Y-m-d") . 'log', 'a');
        fwrite($fp, $msg);
        fclose($fp);
        return true;
    }

    /**
     * 获取表对应的model
     * @return \App\Common\Models\Base\Base
     */
    protected function getData4Collection($collection_id)
    {
        $modelCompanyProject = new \App\Company\Models\Project();
        $modelProjectCollection = new \App\Database\Models\Project\Collection();

        // 获取表信息数据
        $dataCollectionInfo = $modelProjectCollection->getInfoById($collection_id);
        if (empty($dataCollectionInfo)) {
            throw new \Exception("该collection_id:{$collection_id}所对应的集合不存在");
        }
        $companyProjectInfo = $modelCompanyProject->getInfoById($dataCollectionInfo['company_project_id']);
        if (empty($companyProjectInfo)) {
            throw new \Exception('集合的company_project_id无效');
        }
        $objDatabaseManager = new \DatabaseManager($companyProjectInfo['project_code'], $companyProjectInfo['project_code'], $companyProjectInfo['db_pwd']);

        $uniqueDbName = 'data_collection_' . $dataCollectionInfo['_id'] . '_' . \uniqid();
        $di = \Phalcon\DI::getDefault();
        $di->set($uniqueDbName, $objDatabaseManager->getDbConnection4CompanyProject());
        return \App\Common\Models\Base\Base::getEntityModel($dataCollectionInfo['alias'], $uniqueDbName);
    }

    /**
     * 将数组数据导出为csv文件
     *
     * @param array $datas            
     * @param string $name            
     * @param string $output            
     */
    protected function arrayToCVS2($datas, $name = null, $output = null)
    {
        resetTimeMemLimit();
        if (empty($name)) {
            $name = 'export_' . date("Y_m_d_H_i_s");
        }

        $result = array_merge(array(
            $datas['title']
        ), $datas['result']);

        if (empty($output)) {
            $tmpname = tempnam(sys_get_temp_dir(), 'export_csv_');
            $fp = fopen($tmpname, 'w');
            fwrite($fp, "\xEF\xBB\xBF");
            foreach ($result as $row) {
                fputcsv($fp, $row, ",", '"');
            }
            fclose($fp);

            header('Content-type: text/csv;');
            header('Content-Disposition: attachment; filename="' . $name . '.csv"');
            header("Content-Length:" . filesize($tmpname));
            echo file_get_contents($tmpname);
            unlink($tmpname);
            exit();
        } else {
            $fp = fopen($output, 'w');
            fwrite($fp, "\xEF\xBB\xBF");
            foreach ($result as $row) {
                fputcsv($fp, $row, ",", '"');
            }
            fclose($fp);
            return true;
        }
    }

    /**
     * 将指定集合内的数据转化成bson,csv文件
     *
     * @param  $dataModel            
     * @param array $query            
     * @return string
     */
    protected function collection2bson($dataModel, $query = array())
    {
        $list = $dataModel->findAll($query);
        $tmp = tempnam(sys_get_temp_dir(), 'bson_');
        $fp = fopen($tmp, 'w');
        foreach ($list as $row) {
            // fwrite($fp, \bson_encode($row));
            fputcsv($fp, $row);
        }
        fclose($fp);
        return $tmp;
    }

    /**
     * 将项目关系表，导出到指定的文件中
     *
     * @param array $list4Collection            
     * @return string
     */
    private function projectMap2Excel($list4Collection)
    {
        $map = array();
        foreach ($list4Collection as $row) {
            $map[] = array(
                $row['alias'],
                $row['name'],
                $row['desc']
            );
        }

        $tmp = tempnam(sys_get_temp_dir(), 'csv_');
        $datas = array();
        $datas['title'] = array(
            '物理集合',
            '集合名',
            '集合描述'
        );
        $datas['result'] = $map;
        $this->arrayToCVS2($datas, '项目集合对应关系结构', $tmp);
        return $tmp;
    }

    /**
     * 集合数据结构到excel表格
     *
     * @return string
     */
    private function collectionStructure2Excel($collectionInfo)
    {
        $modelProjectCollectionStructure = new \App\Database\Models\Project\Collection\Structure();
        $list4CollectionStructure = $modelProjectCollectionStructure->findAll(
            array(
                'company_project_id' => $collectionInfo['company_project_id'],
                'project_id' => $collectionInfo['project_id'],
                'collection_id' => $collectionInfo['_id']
            )
        );
        $map = array();
        $map[] = array(
            '_id',
            '系统编号',
            '字符串'
        );
        foreach ($list4CollectionStructure as $row) {
            $map[] = array(
                $row['field'],
                $row['label'],
                $row['type']
            );
        }

        $map[] = array(
            '__CREATE_TIME__',
            '创建时间',
            '日期类型'
        );

        $map[] = array(
            '__CREATE_USER_ID__',
            '创建操作者ID',
            '字符串类型'
        );

        $map[] = array(
            '__CREATE_USER_NAME__',
            '创建操作者名',
            '字符串类型'
        );

        $map[] = array(
            '__MODIFY_TIME__',
            '修改时间',
            '日期类型'
        );

        $map[] = array(
            '__MODIFY_USER_ID__',
            '修改操作者ID',
            '字符串类型'
        );

        $map[] = array(
            '__MODIFY_USER_NAME__',
            '修改操作者名',
            '字符串类型'
        );

        $map[] = array(
            '__REMOVED__',
            '是否删除',
            '布尔型'
        );

        $map[] = array(
            '__REMOVE_TIME__',
            '删除时间',
            '日期类型'
        );

        $map[] = array(
            '__REMOVE_USER_ID__',
            '删除操作者ID',
            '字符串类型'
        );

        $map[] = array(
            '__REMOVE_USER_NAME__',
            '删除操作者名',
            '字符串类型'
        );

        $tmp = tempnam(sys_get_temp_dir(), 'csv_');
        $datas = array();
        $datas['title'] = array(
            '字段名',
            '字段说明',
            '字段类型'
        );
        $datas['result'] = $map;
        $this->arrayToCVS2($datas, $collectionInfo['alias'], $tmp);
        return $tmp;
    }
}
