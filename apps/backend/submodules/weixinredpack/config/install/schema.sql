/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.6.35 : Database - webcms
*********************************************************************
*/

/*!40101 SET NAMES utf8mb4 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`webcms` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `webcms`;

/*Table structure for table `iweixinredpack_customer` */

DROP TABLE IF EXISTS `iweixinredpack_customer`;

CREATE TABLE `iweixinredpack_customer` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '客户名称',
  `nick_name` varchar(50) NOT NULL DEFAULT '' COMMENT '提供方名称',
  `send_name` varchar(50) NOT NULL DEFAULT '' COMMENT '商户名称',
  `total_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总金额(分)',
  `used_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已使用金额(分)',
  `remain_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余金额(分)',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信红包-客户';

/*Data for the table `iweixinredpack_customer` */

insert  into `iweixinredpack_customer`(`_id`,`name`,`nick_name`,`send_name`,`total_amount`,`used_amount`,`remain_amount`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56ee23f2ae8715942d000030','测试用户1','测试用户2','测试用户3',1000000,285,999715,'2016-03-20 12:15:46','2016-04-16 12:52:00',0),('57118306a6c2ef0b008b4567','name2','nick_name2','send_name2',40000,40000,0,'2016-04-16 00:10:46','2016-04-16 07:32:28',0);

/*Table structure for table `iweixinredpack_got_log` */

DROP TABLE IF EXISTS `iweixinredpack_got_log`;

CREATE TABLE `iweixinredpack_got_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `mch_billno` char(30) NOT NULL DEFAULT '' COMMENT '商户订单号',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '红包用户ID',
  `re_openid` char(50) NOT NULL DEFAULT '' COMMENT '用户openid',
  `re_nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '微信昵称',
  `re_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '微信头像',
  `client_ip` char(15) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `activity` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `customer` char(24) NOT NULL DEFAULT '' COMMENT '客户',
  `redpack` char(24) NOT NULL DEFAULT '' COMMENT '红包',
  `total_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  `total_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金额(分)',
  `got_time` datetime NOT NULL COMMENT '领取时间',
  `isOK` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否OK',
  `try_count` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '重试次数',
  `is_reissue` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否补发',
  `isNeedSendRedpack` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否正式发送',
  `error_logs` text NOT NULL COMMENT '错误日志记录',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信红包-发放记录';

/*Data for the table `iweixinredpack_got_log` */

insert  into `iweixinredpack_got_log`(`_id`,`mch_billno`,`user_id`,`re_openid`,`re_nickname`,`re_headimgurl`,`client_ip`,`activity`,`customer`,`redpack`,`total_num`,`total_amount`,`got_time`,`isOK`,`try_count`,`is_reissue`,`isNeedSendRedpack`,`error_logs`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('571233dfa6c2ef0b008b4568','201604164067523037','','ooCkDj-Knv4XWlRa2jjOwtspd7pk','郭永荣','','192.168.222.1','565d5aaa7f50ea081300002d','56ee23f2ae8715942d000030','56ee254dae8715942d000031',1,153,'2016-04-16 12:45:19',1,0,1,0,'','{\"is_test\":\"\\u6d4b\\u8bd5\\u7528\",\"total_amount\":\"153\",\"return_code\":\"SUCCESS\",\"return_msg\":\"\\u53d1\\u653e\\u6210\\u529f\",\"result_code\":\"SUCCESS\",\"err_code\":\"0\",\"err_code_des\":\"SUCCESS\",\"mch_billno\":\"201604164067523037\",\"re_openid\":\"ooCkDj-Knv4XWlRa2jjOwtspd7pk\",\"_id\":\"571233dfa6c2ef0b008b4568\",\"user_id\":\"\",\"re_nickname\":\"\\u90ed\\u6c38\\u8363\",\"re_headimgurl\":\"\",\"client_ip\":\"192.168.222.1\",\"activity\":\"565d5aaa7f50ea081300002d\",\"customer\":\"56ee23f2ae8715942d000030\",\"redpack\":\"56ee254dae8715942d000031\",\"total_num\":\"1\",\"got_time\":{\"sec\":1460810719,\"usec\":0},\"isOK\":false,\"try_count\":\"0\",\"is_reissue\":\"0\",\"isNeedSendRedpack\":\"0\",\"error_logs\":[],\"memo\":{\"openid\":\"ooCkDj-Knv4XWlRa2jjOwtspd7pk\",\"nickname\":\"\\u90ed\\u6c38\\u8363\",\"headimgurl\":\"\",\"nick_name\":\"\\u63d0\\u4f9b\\u65b9\\u540d\\u79f0\",\"send_name\":\"\\u5546\\u6237\\u540d\\u79f0\",\"min_value\":153,\"max_value\":153,\"wishing\":\"\\u7ea2\\u5305\\u795d\\u798f\",\"act_id\":\"redpack001\",\"act_name\":\"\\u7ea2\\u53051\",\"remark\":\"\\u7ea2\\u5305\\u5907\\u6ce8\",\"logo_imgurl\":\"http:\\/\\/www.baidu.com\\/\",\"share_content\":\"\\u5206\\u4eab\\u6587\\u6848\",\"share_url\":\"http:\\/\\/www.baidu.com\\/\",\"share_imgurl\":\"http:\\/\\/www.baidu.com\\/\"},\"__CREATE_TIME__\":{\"sec\":1460810719,\"usec\":0},\"__MODIFY_TIME__\":{\"sec\":1460810719,\"usec\":0},\"__REMOVED__\":false}','2016-04-16 12:45:19','2016-04-16 12:45:20',0),('57123570a6c2ef08008b4568','201604166664824602','','ccccc','郭永荣','','192.168.222.1','565d5aaa7f50ea081300002d','56ee23f2ae8715942d000030','56ee254dae8715942d000031',1,132,'2016-04-16 12:52:00',1,0,1,0,'','{\"is_test\":\"\\u6d4b\\u8bd5\\u7528\",\"total_amount\":\"132\",\"return_code\":\"SUCCESS\",\"return_msg\":\"\\u53d1\\u653e\\u6210\\u529f\",\"result_code\":\"SUCCESS\",\"err_code\":\"0\",\"err_code_des\":\"SUCCESS\",\"mch_billno\":\"201604166664824602\",\"re_openid\":\"ccccc\",\"_id\":\"57123570a6c2ef08008b4568\",\"user_id\":\"\",\"re_nickname\":\"\\u90ed\\u6c38\\u8363\",\"re_headimgurl\":\"\",\"client_ip\":\"192.168.222.1\",\"activity\":\"565d5aaa7f50ea081300002d\",\"customer\":\"56ee23f2ae8715942d000030\",\"redpack\":\"56ee254dae8715942d000031\",\"total_num\":\"1\",\"got_time\":{\"sec\":1460811120,\"usec\":0},\"isOK\":false,\"try_count\":\"0\",\"is_reissue\":\"0\",\"isNeedSendRedpack\":\"0\",\"error_logs\":[],\"memo\":{\"openid\":\"ccccc\",\"nickname\":\"\\u90ed\\u6c38\\u8363\",\"headimgurl\":\"\",\"nick_name\":\"\\u63d0\\u4f9b\\u65b9\\u540d\\u79f0\",\"send_name\":\"\\u5546\\u6237\\u540d\\u79f0\",\"min_value\":132,\"max_value\":132,\"wishing\":\"\\u7ea2\\u5305\\u795d\\u798f\",\"act_id\":\"redpack001\",\"act_name\":\"\\u7ea2\\u53051\",\"remark\":\"\\u7ea2\\u5305\\u5907\\u6ce8\",\"logo_imgurl\":\"http:\\/\\/www.baidu.com\\/\",\"share_content\":\"\\u5206\\u4eab\\u6587\\u6848\",\"share_url\":\"http:\\/\\/www.baidu.com\\/\",\"share_imgurl\":\"http:\\/\\/www.baidu.com\\/\"},\"__CREATE_TIME__\":{\"sec\":1460811120,\"usec\":0},\"__MODIFY_TIME__\":{\"sec\":1460811120,\"usec\":0},\"__REMOVED__\":false}','2016-04-16 12:52:00','2016-04-16 12:52:00',0);

/*Table structure for table `iweixinredpack_limit` */

DROP TABLE IF EXISTS `iweixinredpack_limit`;

CREATE TABLE `iweixinredpack_limit` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `customer` char(24) NOT NULL DEFAULT '' COMMENT '客户',
  `redpack` char(24) NOT NULL DEFAULT '' COMMENT '红包',
  `personal_got_num_limit` smallint(1) unsigned NOT NULL DEFAULT '1' COMMENT '每人获取数量限制',
  `start_time` datetime NOT NULL COMMENT '限制开始时间',
  `end_time` datetime NOT NULL COMMENT '限制结束时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信红包-活动规则限制';

/*Data for the table `iweixinredpack_limit` */

insert  into `iweixinredpack_limit`(`_id`,`activity`,`customer`,`redpack`,`personal_got_num_limit`,`start_time`,`end_time`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56ee2568ae8715942d000032','565d5aaa7f50ea081300002d','56ee23f2ae8715942d000030','56ee254dae8715942d000031',1,'2016-03-20 00:00:00','2016-05-21 23:59:59','2016-03-20 12:22:00','2016-03-20 12:22:00',0);

/*Table structure for table `iweixinredpack_redpack` */

DROP TABLE IF EXISTS `iweixinredpack_redpack`;

CREATE TABLE `iweixinredpack_redpack` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `code` char(24) NOT NULL DEFAULT '' COMMENT '红包代码',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '红包名',
  `desc` text NOT NULL COMMENT '说明',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='微信红包-红包';

/*Data for the table `iweixinredpack_redpack` */

insert  into `iweixinredpack_redpack`(`_id`,`code`,`name`,`desc`,`start_time`,`end_time`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56ee254dae8715942d000031','redpack001','红包1','<p>说明1</p>','0000-00-00 00:00:00','0000-00-00 00:00:00','2016-03-20 12:21:33','2016-03-20 12:21:33',0);

/*Table structure for table `iweixinredpack_reissue` */

DROP TABLE IF EXISTS `iweixinredpack_reissue`;

CREATE TABLE `iweixinredpack_reissue` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `logid` char(24) NOT NULL DEFAULT '' COMMENT '红包日志ID',
  `redpack` text NOT NULL COMMENT '红包日志',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信红包-补发日志';

/*Data for the table `iweixinredpack_reissue` */

/*Table structure for table `iweixinredpack_rule` */

DROP TABLE IF EXISTS `iweixinredpack_rule`;

CREATE TABLE `iweixinredpack_rule` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `customer` char(24) NOT NULL DEFAULT '' COMMENT '客户',
  `redpack` char(24) NOT NULL DEFAULT '' COMMENT '红包',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '红包发放总金额(分)',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '红包发放总数量',
  `min_cash` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最小金额(分)',
  `max_cash` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大金额(分)',
  `allow_probability` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '概率(N/10000)',
  `personal_can_get_num` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '最大数量(人)',
  `nick_name` varchar(50) NOT NULL DEFAULT '' COMMENT '提供方名称',
  `send_name` varchar(50) NOT NULL DEFAULT '' COMMENT '商户名称',
  `wishing` varchar(300) NOT NULL DEFAULT '' COMMENT '红包祝福',
  `remark` varchar(300) NOT NULL DEFAULT '' COMMENT '备注',
  `logo_imgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '商户logo',
  `share_content` varchar(300) NOT NULL DEFAULT '' COMMENT '分享文案',
  `share_url` varchar(300) NOT NULL DEFAULT '' COMMENT '分享链接 ',
  `share_imgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '分享的图片',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信红包-红包发放规则';

/*Data for the table `iweixinredpack_rule` */

insert  into `iweixinredpack_rule`(`_id`,`activity`,`customer`,`redpack`,`start_time`,`end_time`,`amount`,`quantity`,`min_cash`,`max_cash`,`allow_probability`,`personal_can_get_num`,`nick_name`,`send_name`,`wishing`,`remark`,`logo_imgurl`,`share_content`,`share_url`,`share_imgurl`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56ee25f4ae8715ec26000029','565d5aaa7f50ea081300002d','56ee23f2ae8715942d000030','56ee254dae8715942d000031','2016-03-20 00:00:00','2016-05-21 23:59:59',999715,98,100,200,10000,1,'提供方名称','商户名称','红包祝福','红包备注','http://www.baidu.com/','分享文案','http://www.baidu.com/','http://www.baidu.com/','2016-03-20 12:24:20','2016-04-16 12:52:00',0);

/*Table structure for table `iweixinredpack_user` */

DROP TABLE IF EXISTS `iweixinredpack_user`;

CREATE TABLE `iweixinredpack_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `FromUserName` char(50) NOT NULL DEFAULT '' COMMENT '活动微信用户ID',
  `re_openid` char(50) NOT NULL DEFAULT '' COMMENT '红包微信用户ID',
  `withdraw_date` char(8) NOT NULL DEFAULT '' COMMENT '提现日期',
  `withdraw_money` int(10) NOT NULL DEFAULT '0' COMMENT '提现金额',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`FromUserName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信红包-红包用户';

/*Data for the table `iweixinredpack_user` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
