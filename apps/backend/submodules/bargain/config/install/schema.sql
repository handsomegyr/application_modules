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

/*Table structure for table `ibargain_alpha_user` */

DROP TABLE IF EXISTS `ibargain_alpha_user`;

CREATE TABLE `ibargain_alpha_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '日志ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `alpha` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '阿尔法系数',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  KEY `NewIndex1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='砍价-砍价用户系数表';

/*Data for the table `ibargain_alpha_user` */

/*Table structure for table `ibargain_bargain` */

DROP TABLE IF EXISTS `ibargain_bargain`;

CREATE TABLE `ibargain_bargain` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '规则ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '所属活动',
  `user_id` varchar(50) NOT NULL DEFAULT '' COMMENT '砍价物发起用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '砍价物发起用户名',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '砍价物发起用户头像',
  `code` char(24) NOT NULL DEFAULT '' COMMENT '砍价物编号',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '砍价物名称',
  `worth` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '砍价物价值(分)',
  `quantity` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  `launch_time` datetime NOT NULL COMMENT '砍价物发起时间',
  `bargain_from` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '砍价区间(从)',
  `bargain_to` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '砍价区间(至)',
  `worth_min` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最低价值(分)',
  `bargain_max` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '砍价极限金额(分)',
  `bargain_period` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '能砍价的时间段(小时)',
  `is_both_bargain` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否双向砍',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `bargain_num_limit` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '砍价限制次数,0无限制',
  `current_worth` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最终价值(分)',
  `is_bargain_to_minworth` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已砍到最低价值',
  `bargain_to_minworth_time` datetime NOT NULL COMMENT '砍到最低价值时间',
  `is_closed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已下线',
  `total_bargain_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总砍价次数',
  `total_bargain_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总砍价金额(分)',
  `memo` text NOT NULL COMMENT '活动配置',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  KEY `NewIndex1` (`user_id`,`code`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='砍价-砍价物规则';

/*Data for the table `ibargain_bargain` */

insert  into `ibargain_bargain`(`_id`,`activity_id`,`user_id`,`user_name`,`user_headimgurl`,`code`,`name`,`worth`,`quantity`,`launch_time`,`bargain_from`,`bargain_to`,`worth_min`,`bargain_max`,`bargain_period`,`is_both_bargain`,`start_time`,`end_time`,`bargain_num_limit`,`current_worth`,`is_bargain_to_minworth`,`bargain_to_minworth_time`,`is_closed`,`total_bargain_num`,`total_bargain_amount`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('58d4dd2af3b9530d008b4568','58d4d7b4f3b9530b008b4567','guoyongrong','guoyongrong','headimgurl','bargain_code1','bargain_name1',1000,1,'2017-03-24 16:47:38',100,200,500,800,24,0,'2017-03-24 00:00:00','2019-03-24 00:00:00',10,832,0,'2017-03-24 16:47:38',1,1,168,'','2017-03-24 16:47:38','2017-03-24 17:17:50',0);

/*Table structure for table `ibargain_black_user` */

DROP TABLE IF EXISTS `ibargain_black_user`;

CREATE TABLE `ibargain_black_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '日志ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `alpha` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '阿尔法系数',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  KEY `NewIndex1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='砍价-砍价用户惩罚系数表';

/*Data for the table `ibargain_black_user` */

/*Table structure for table `ibargain_log` */

DROP TABLE IF EXISTS `ibargain_log`;

CREATE TABLE `ibargain_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '日志ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `client_ip` char(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `bargain_id` char(24) NOT NULL DEFAULT '' COMMENT '砍价物ID',
  `bargain_time` datetime NOT NULL COMMENT '砍价时间',
  `bargain_num` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '砍价次数',
  `bargain_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '砍价金额(分)',
  `is_system_bargain` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否系统砍',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  KEY `NewIndex1` (`user_id`,`bargain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='砍价-日志';

/*Data for the table `ibargain_log` */

insert  into `ibargain_log`(`_id`,`user_id`,`user_name`,`user_headimgurl`,`client_ip`,`bargain_id`,`bargain_time`,`bargain_num`,`bargain_amount`,`is_system_bargain`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('58d4e1a0f3b953ed008b4568','handsomegyr','handsomegyr','handsomegyr','192.168.81.1','58d4dd2af3b9530d008b4568','2017-03-24 17:06:40',1,168,0,'{\"bargain_code\":\"bargain_code1\",\"bargain_name\":\"bargain_name1\",\"random\":7714}','2017-03-24 17:06:40','2017-03-24 17:06:40',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
