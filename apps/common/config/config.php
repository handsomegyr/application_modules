<?php
return new \Phalcon\Config(array(
    'database' => array(
        'adapter' => 'Mysql',
        'host' => '139.196.170.93',
        'username' => 'webcms',
        'password' => 'webcmsQWS123',
        'dbname' => 'webcms',
        'charset' => 'utf8'
    ),
    // 'database' => array(
    // 'adapter' => 'Mysql',
    // 'host' => 'localhost',
    // 'username' => 'root',
    // 'password' => 'guotingyu0324',
    // 'dbname' => 'webcms',
    // 'charset' => 'utf8'
    // ),
    'databasefrom' => array(
        'adapter' => 'Mysql',
        'host' => '139.196.170.93',
        'username' => 'webcms',
        'password' => 'webcmsQWS123',
        'dbname' => 'shopnc',
        'charset' => 'utf8'
    ),
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
