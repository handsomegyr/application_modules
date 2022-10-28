<?php
return new \Phalcon\Config\Config(array(
    'database' => array(
        'adapter' => 'Mysql',
        'host' => '192.168.81.129',
        'username' => 'root',
        'password' => 'guotingyu0324',
        'dbname' => 'webcms',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ),
    'database4admin' => array(
        'adapter' => 'Mysql',
        'host' => '192.168.81.129',
        'username' => 'root',
        'password' => 'guotingyu0324',
        'dbname' => 'mysql',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ),
    // 'database' => array(
    // 'adapter' => 'Mysql',
    // 'host' => '192.168.81.129',
    // 'username' => 'root',
    // 'password' => 'guotingyu0324',
    // 'dbname' => 'webcms',
    // 'charset' => 'utf8mb4',
    // 'collation' => 'utf8mb4_unicode_ci'
    // ),
    'databasefrom' => array(
        'adapter' => 'Mysql',
        'host' => '192.168.81.129',
        'username' => 'root',
        'password' => 'guotingyu0324',
        'dbname' => 'shopnc',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ),
    'mongodb' => array(
        'uri' => '192.168.81.129:27017',
        'dbname' => 'webcms'
    ),
    'weixin' => array(
        "appid" => 'wxe735383666834fc9',
        "get_accesstoken_url" => "http://weixinoauth.umaman.com/weixin/index/get-access-token",
        'openid4developer' => 'on8S7uINhQNNenv2AUE7ytD18eNs'
    ),
    'weixinAuthorize' => array(
        'authorizeUrl' => 'http://www.myapplicationmodule.com/weixin/sns/index',
        'secretKey' => 'jizigou',
        'scope' => 'snsapi_userinfo'
    ),
    'tencentAuthorize' => array(
        'authorizeUrl' => 'http://www.myapplicationmodule.com/tencent/sns/index',
        'secretKey' => '12345667890',
        'scope' => 'get_user_info'
    ),
    'tencent' => array(
        "appid" => 'wxbf9165206b992f39'
    ),
    'alipayAuthorize' => array(
        'authorizeUrl' => 'http://www.myapplicationmodule.com/alipay/sns/index',
        'secretKey' => 'jizigou',
        'scope' => 'auth_user'
    ),
    'alipay' => array(
        "appid" => '2017071707783020'
    ),
    'memcached' => array(
        "host" => '192.168.81.129',
        "port" => 11211,
        "weight" => 1,
        "prefix_key" => 'webcms'
    ),
    'pheanstalk' => array(
        "host" => '192.168.81.129',
        "port" => 11300
    ),
    'elasticsearch' => array(
        "host" => '192.168.81.129',
        "port" => 11300
    ),
    'redis' => array(
        "host" => '192.168.81.129',
        "port" => 6379
    ),
    // 腾讯乐享
    'lexiangla' => array(
        //开发者ID(AppKey)
        'AppKey' => '',
        //开发者密码(AppSecret)
        'AppSecret' => '',
        //公司Code
        'company_code' => '',
        //StaffID
        'staffID' => '',
        //回调密钥 (Secret)
        'secret' => ''
    ),
    'cronjobdataimport' => [
        // 订单数据导入
        "orderDataImput" => [
            //测试环境
            'test' => [
                // 文件所在目录
                'ftpDirectory' => '/learn-php/phalcon/application_modules/data/upload/',
                // 程序处理完毕后会将文件移至
                'backupDirectory' => '/learn-php/phalcon/application_modules/data/bak/',
            ],
            //本地环境
            'local' => [
                // 文件所在目录
                'ftpDirectory' => '/learn-php/phalcon/application_modules/data/upload/',
                // 程序处理完毕后会将文件移至
                'backupDirectory' => '/learn-php/phalcon/application_modules/data/bak/',
            ],
            //正式环境
            'production' => [
                // 文件所在目录
                'ftpDirectory' => '/learn-php/phalcon/application_modules/data/upload/',

                // 程序处理完毕后会将文件移至
                'backupDirectory' => '/learn-php/phalcon/application_modules/data/bak/',
            ],

            // csv文件名
            'csvFile' => 'zhoubiandingdan_%s.csv',
            // 上传成功标志文件名
            'successFlagFile' => 'success_%s.flag',
            // 文件内容类型
            'content_type' => "order",
            // 第一行是否是头
            'firstrow_is_header' => 1,

        ]
    ],
    'errors' => array(
        'none' => array(
            'error_code' => 0,
            'error_msg' => ''
        ),
        'e599' => array(
            'error_code' => 599,
            'error_msg' => '验证码错误'
        ),
        'e598' => array(
            'error_code' => 598,
            'error_msg' => '验证码错误'
        ),
        'e597' => array(
            'error_code' => 597,
            'error_msg' => '非法提交'
        ),
        'e596' => array(
            'error_code' => 596,
            'error_msg' => '用户名或手机号或邮箱不能为空'
        ),
        'e595' => array(
            'error_code' => 595,
            'error_msg' => '账号未登录'
        ),
        'e501' => array(
            'error_code' => 501,
            'error_msg' => '用户名不能为空'
        ),
        'e502' => array(
            'error_code' => 502,
            'error_msg' => '用户名已存在'
        ),
        'e503' => array(
            'error_code' => 503,
            'error_msg' => '密码不能为空'
        ),
        'e504' => array(
            'error_code' => 504,
            'error_msg' => '确认密码不能为空'
        ),
        'e505' => array(
            'error_code' => 505,
            'error_msg' => '密码与确认密码不相同'
        ),
        'e506' => array(
            'error_code' => 506,
            'error_msg' => '邮箱地址不能为空或格式不正确'
        ),
        'e507' => array(
            'error_code' => 507,
            'error_msg' => '邮箱地址已存在'
        ),
        'e508' => array(
            'error_code' => 508,
            'error_msg' => '手机号不能为空或格式不正确'
        ),
        'e509' => array(
            'error_code' => 509,
            'error_msg' => '手机号已存在'
        ),
        'e510' => array(
            'error_code' => 510,
            'error_msg' => '用户名或密码有误'
        ),
        'e511' => array(
            'error_code' => 511,
            'error_msg' => '账号被停用'
        ),
        'e512' => array(
            'error_code' => 512,
            'error_msg' => '该用户不存在'
        ),
        'e513' => array(
            'error_code' => 513,
            'error_msg' => '验证码为空或不正确或已过期'
        ),
        'e514' => array(
            'error_code' => 514,
            'error_msg' => '密码有误'
        ),
        'e515' => array(
            'error_code' => 515,
            'error_msg' => '该用户还未验证'
        ),
        'e516' => array(
            'error_code' => 516,
            'error_msg' => ''
        ),
        'e517' => array(
            'error_code' => 517,
            'error_msg' => ''
        )
    )
));
