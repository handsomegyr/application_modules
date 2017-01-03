/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.6.26 : Database - webcms
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`webcms` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `webcms`;

/*Table structure for table `iactivity_activity` */

DROP TABLE IF EXISTS `iactivity_activity`;

CREATE TABLE `iactivity_activity` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '活动分类',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '活动名称',
  `start_time` datetime NOT NULL COMMENT '活动开始时间',
  `end_time` datetime NOT NULL COMMENT '活动结束时间',
  `is_actived` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否激活',
  `is_paused` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否暂停',
  `config` text NOT NULL COMMENT '活动配置',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='活动-活动';

/*Data for the table `iactivity_activity` */

insert  into `iactivity_activity`(`_id`,`category`,`name`,`start_time`,`end_time`,`is_actived`,`is_paused`,`config`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5861e812887c22015f8b456b',3,'某抽奖活动','2016-12-27 00:00:00','2019-12-27 14:22:20',1,0,'','2016-12-27 12:03:30','2016-12-27 14:22:31',0);

/*Table structure for table `iactivity_black_user` */

DROP TABLE IF EXISTS `iactivity_black_user`;

CREATE TABLE `iactivity_black_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '黑名单用户ID',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='活动-活动黑名单用戶';

/*Data for the table `iactivity_black_user` */

/*Table structure for table `iactivity_category` */

DROP TABLE IF EXISTS `iactivity_category`;

CREATE TABLE `iactivity_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `code` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分类标识码',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='活动-分类';

/*Data for the table `iactivity_category` */

insert  into `iactivity_category`(`_id`,`code`,`name`,`sort`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5861e657887c2224678b4582',1,'组团',1,'2016-12-27 11:56:07','2016-12-27 11:56:07',0),('5861e737887c22a1268b4569',2,'红包',2,'2016-12-27 11:59:51','2016-12-27 11:59:51',0),('5861e74d887c222c688b4576',3,'抽奖',3,'2016-12-27 12:00:13','2016-12-27 12:00:13',0),('5861e758887c2223678b4597',4,'兑换',4,'2016-12-27 12:00:24','2016-12-27 12:00:24',0);

/*Table structure for table `iactivity_errorlog` */

DROP TABLE IF EXISTS `iactivity_errorlog`;

CREATE TABLE `iactivity_errorlog` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '错误记录ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `error_code` int(11) NOT NULL DEFAULT '0' COMMENT '错误码',
  `error_message` varchar(1000) NOT NULL DEFAULT '' COMMENT '错误信息',
  `happen_time` datetime NOT NULL COMMENT '发生时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='活动错误日志';

/*Data for the table `iactivity_errorlog` */

/*Table structure for table `iactivity_user` */

DROP TABLE IF EXISTS `iactivity_user`;

CREATE TABLE `iactivity_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `worth` int(10) NOT NULL DEFAULT '0' COMMENT '价值',
  `worth2` int(11) NOT NULL DEFAULT '0' COMMENT '价值2',
  `redpack_user` char(50) NOT NULL DEFAULT '' COMMENT '微信红包账号',
  `thirdparty_user` char(50) NOT NULL DEFAULT '' COMMENT '第3方账号',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='活动-活动用戶';

/*Data for the table `iactivity_user` */

insert  into `iactivity_user`(`_id`,`activity_id`,`user_id`,`nickname`,`headimgurl`,`worth`,`worth2`,`redpack_user`,`thirdparty_user`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5865eed5fcc2b6e8008b4568','5861e812887c22015f8b456b','xxxx','xx','xx',0,0,'redpack_user','thirdparty_user','{\"is_got_prize\":true,\"is_record_lottery_user_contact_info\":true,\"prizeInfo\":{\"_id\":\"5865f1edfcc2b60a008b456c\",\"activity_id\":\"5861e812887c22015f8b456b\",\"user_id\":\"xxxx\",\"prize_id\":\"569b85bf887c22cf6c8b46d4\",\"is_valid\":\"1\",\"got_time\":{\"sec\":1483076077,\"usec\":0},\"source\":\"weixin\",\"prize_code\":\"569b85bf887c22cf6c8b46d3\",\"prize_name\":\"\\u4f18\\u60e0\\u52381\",\"prize_category\":\"1\",\"prize_virtual_currency\":\"10\",\"prize_is_virtual\":\"1\",\"prize_virtual_code\":\"10000002\",\"prize_virtual_pwd\":\"1234\",\"user_name\":\"xx\",\"user_headimgurl\":\"xx\",\"contact_name\":\"\",\"contact_mobile\":\"\",\"contact_address\":\"\",\"memo\":{\"activity_user_id\":\"5865eed5fcc2b6e8008b4568\"},\"__CREATE_TIME__\":{\"sec\":1483076077,\"usec\":0},\"__MODIFY_TIME__\":{\"sec\":1483076077,\"usec\":0},\"__REMOVED__\":false,\"exchange_id\":\"5865f1edfcc2b60a008b456c\"}}','2016-12-30 13:21:05','2016-12-30 13:42:45',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
