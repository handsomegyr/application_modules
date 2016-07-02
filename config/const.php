<?php
date_default_timezone_set('Asia/Chongqing');

defined('APP_PATH') || define('APP_PATH', dirname(dirname(__FILE__)) . '/');
defined('APP_PAY_PATH') || define('APP_PAY_PATH', APP_PATH . 'library/pay/');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'development'); // [production|development]

define('POINTS_CATEGORY1', 1); // 福分
define('POINTS_CATEGORY2', 2); // 经验值
define('POINTS_CATEGORY3', 3); // 预存款
define('YUNGOU_ACTIVITY_ID', '565d5aaa7f50ea081300002d'); // 云购
define('YUNGOU_ORDER_STATISTICS_ID', '568149f3887c226e6a8b56fd'); // 云购订单统计ID
define('YUNGOU_SITE_ID', '563df213887c22d0498b456f'); // 云购网站ID