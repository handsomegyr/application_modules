<?php
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

    // 文件同步相关
    //protected $RSYNCCMD = "/bin/env USER='root' RSYNC_PASSWORD='guotingyu0324' /usr/bin/rsync -azu --progress --delete --exclude='.svn' --exclude='.git' --exclude='.buildpath' --exclude='.project' --exclude='.gitignore' --exclude='*.log' --exclude='/logs/*' --exclude='/cache/*' --exclude=node_modules "; //--blocking-io
    protected $RSYNCCMD = "rsync -azu --delete --exclude=*.log --exclude=.svn  --exclude=*.svn ";
    protected $RSYNCSERVER = '192.168.81.129';
    protected $IPS = array('192.168.81.129');
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
                    if (intval($taskInfo['do_num']) >= 3) {
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
                    if (!file_exists($realm . "/conf/svnserve.conf")) {
                        $config4SvnServer = str_replace("#_realm_#", $realm, $this->SVNSERVER_CONFIG_TEMPLATE);
                        $cmdline = "file_put_contents to {$realm}/conf/svnserve.conf";
                        $tip = file_put_contents($realm . "/conf/svnserve.conf", $config4SvnServer);
                    }
                } elseif ($process_name == 'svn_conf_post_comit') {
                    // 创建hooks/post-commit文件                        
                    $realm = $this->SVNROOT_R . $project_code;
                    if (!file_exists($realm . "/hooks/post-commit")) {
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
                    if (!file_exists($file4Nginx)) {
                        $config4Nginx = $this->NGINX_CONFIG_TEMPLATE;
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
                    if (!file_exists($file4Nginx)) {
                        $config4Nginx = $this->NGINX_CONFIG_TEMPLATE;
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
                    if (!file_exists($file4Nginx)) {
                        $config4Nginx = $this->NGINX_CONFIG_TEMPLATE;
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
                    // $di = $this->getDI();
                    $di = \Phalcon\DI::getDefault();
                    $config = $di->get('config');
                    // 创建项目对应的数据库
                    $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                        "host" => $config->database->host,
                        "username" => $config->database->username,
                        "password" => $config->database->password,
                        // "dbname" => $config->database->dbname,
                        "charset" => $config->database->charset,
                        "collation" => $config->database->collation,
                        'options'  => [
                            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config->database->charset} COLLATE {$config->database->collation};",
                            //\PDO::ATTR_CASE => PDO::CASE_LOWER,
                        ],
                    ));
                    $ret = $connection->execute("CREATE DATABASE IF NOT EXISTS `?` DEFAULT CHARACTER SET ? COLLATE ? ", array(
                        $project_code,
                        $config->database->charset,
                        $config->database->collation,
                    ));
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
}
