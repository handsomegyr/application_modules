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

/*Table structure for table `ivote_category` */

DROP TABLE IF EXISTS `ivote_category`;

CREATE TABLE `ivote_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '类别ID',
  `code` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型值',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '类型名称',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投票-类型';

/*Data for the table `ivote_category` */

insert  into `ivote_category`(`_id`,`code`,`name`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56de843c7f50ea7c14000029',1,'单选','2016-03-08 15:50:20','2016-03-08 15:50:20',0),('56de84477f50ea2c0a000029',2,'多选','2016-03-08 15:50:31','2016-03-08 15:50:31',0);

/*Table structure for table `ivote_item` */

DROP TABLE IF EXISTS `ivote_item`;

CREATE TABLE `ivote_item` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '选项ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `desc` text NOT NULL COMMENT '说明',
  `subject_id` char(24) NOT NULL DEFAULT '' COMMENT '所属主题',
  `vote_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票次数',
  `is_closed` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否关闭',
  `show_order` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序(从大到小)',
  `rank_period` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排行期数',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投票-选项';

/*Data for the table `ivote_item` */

insert  into `ivote_item`(`_id`,`name`,`desc`,`subject_id`,`vote_count`,`is_closed`,`show_order`,`rank_period`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56dea0517f50ea3812000029','选项1','<p>选项1</p>','56de9e0a7f50ea8411000029',1,0,1,1,'','2016-03-08 17:50:09','2016-03-09 15:35:39',0);

/*Table structure for table `ivote_limit` */

DROP TABLE IF EXISTS `ivote_limit`;

CREATE TABLE `ivote_limit` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '限制ID',
  `category` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '限制类别',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `limit_count` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '限制次数',
  `activity` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `subject` char(24) NOT NULL DEFAULT '' COMMENT '主题',
  `item` char(24) NOT NULL DEFAULT '' COMMENT '选项',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投票-限制';

/*Data for the table `ivote_limit` */

insert  into `ivote_limit`(`_id`,`category`,`start_time`,`end_time`,`limit_count`,`activity`,`subject`,`item`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56dea7607f50ea440a000029',3,'2016-03-08 00:00:00','2016-03-09 23:59:59',10,'565d5aaa7f50ea081300002d','56de9e0a7f50ea8411000029','56dea0517f50ea3812000029','2016-03-08 18:20:15','2016-03-08 18:20:15',0);

/*Table structure for table `ivote_limit_category` */

DROP TABLE IF EXISTS `ivote_limit_category`;

CREATE TABLE `ivote_limit_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '限制类别ID',
  `category` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '限制类别值',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名称',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投票-限制类别';

/*Data for the table `ivote_limit_category` */

insert  into `ivote_limit_category`(`_id`,`category`,`name`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56de862d7f50ea2412000029',1,'IP','2016-03-08 15:58:37','2016-03-08 15:58:37',0),('56de86367f50ea440500002b',2,'会话','2016-03-08 15:58:46','2016-03-08 15:58:46',0),('56de863f7f50ea601100002a',3,'身份','2016-03-08 15:58:55','2016-03-08 15:58:55',0);

/*Table structure for table `ivote_log` */

DROP TABLE IF EXISTS `ivote_log`;

CREATE TABLE `ivote_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '明细ID',
  `activity` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `subject` char(24) NOT NULL DEFAULT '' COMMENT '投票主题',
  `item` char(24) NOT NULL DEFAULT '' COMMENT '选项',
  `vote_time` datetime NOT NULL COMMENT '投票时间',
  `identity` char(50) NOT NULL DEFAULT '' COMMENT '投票凭证',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `session_id` char(30) NOT NULL DEFAULT '' COMMENT '会话ID',
  `vote_num` smallint(1) unsigned NOT NULL DEFAULT '1' COMMENT '投票次数',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投票-明细';

/*Data for the table `ivote_log` */

insert  into `ivote_log`(`_id`,`activity`,`subject`,`item`,`vote_time`,`identity`,`ip`,`session_id`,`vote_num`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56dfd0997f50ead016000029','565d5aaa7f50ea081300002d','56de9e0a7f50ea8411000029','56dea0517f50ea3812000029','2016-03-09 15:28:18','xxxx','127.0.0.1','reqladu9gl5865bi597sn9cpa4',1,'','2016-03-09 15:28:18','2016-03-09 15:28:18',0);

/*Table structure for table `ivote_period` */

DROP TABLE IF EXISTS `ivote_period`;

CREATE TABLE `ivote_period` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '排行期ID',
  `subject_id` char(24) NOT NULL DEFAULT '' COMMENT '投票主题ID',
  `period` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '当前期数',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投票-排行期';

/*Data for the table `ivote_period` */

insert  into `ivote_period`(`_id`,`subject_id`,`period`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56dfd24f7f50eaf410000029','56de9e0a7f50ea8411000029',1,'2016-03-09 15:35:39','2016-03-09 15:35:39',0);

/*Table structure for table `ivote_rank_period` */

DROP TABLE IF EXISTS `ivote_rank_period`;

CREATE TABLE `ivote_rank_period` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '每期排行ID',
  `subject_id` char(24) NOT NULL DEFAULT '' COMMENT '投票主题ID',
  `period` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '期数',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `desc` text NOT NULL COMMENT '详细',
  `vote_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票数',
  `show_order` smallint(1) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投票-每期排行';

/*Data for the table `ivote_rank_period` */

insert  into `ivote_rank_period`(`_id`,`subject_id`,`period`,`name`,`desc`,`vote_count`,`show_order`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56dfd2507f50eaf41000002a','56de9e0a7f50ea8411000029',1,'选项1','<p>选项1</p>',1,1,'','2016-03-09 15:35:39','2016-03-09 15:35:39',0);

/*Table structure for table `ivote_subject` */

DROP TABLE IF EXISTS `ivote_subject`;

CREATE TABLE `ivote_subject` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '主题ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '主题名',
  `desc` text NOT NULL COMMENT '描述',
  `vote_category` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '投票类型',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `is_closed` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否关闭',
  `vote_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票次数',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '所属活动',
  `show_order` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序(从大到小)',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投票-主题';

/*Data for the table `ivote_subject` */

insert  into `ivote_subject`(`_id`,`name`,`desc`,`vote_category`,`start_time`,`end_time`,`is_closed`,`vote_count`,`activity_id`,`show_order`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56de9e0a7f50ea8411000029','主题1','<p>主题1</p>',1,'2016-03-08 00:00:00','2016-03-09 23:59:59',0,1,'565d5aaa7f50ea081300002d',1,'','2016-03-08 17:40:26','2016-03-09 15:28:18',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
