/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.6.35 : Database - webcms
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

/*Table structure for table `isign_log` */

DROP TABLE IF EXISTS `isign_log`;

CREATE TABLE `isign_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '所属活动',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `sign_time` datetime NOT NULL COMMENT '签到时间',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='签到-签到日志';

/*Data for the table `isign_log` */

insert  into `isign_log`(`_id`,`activity_id`,`user_id`,`nickname`,`headimgurl`,`sign_time`,`ip`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('59bf74459fff638c098b4568','59bde95f9fff63070a8b4567','oFTgHwbw1xwUz8MgIBLW74kXcqnY','xxx','xxx','2017-09-18 15:22:45','192.168.81.1','{\"xxx\":\"\"}','2017-09-18 15:22:45','2017-09-18 15:22:45',0);

/*Table structure for table `isign_sign` */

DROP TABLE IF EXISTS `isign_sign`;

CREATE TABLE `isign_sign` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '所属活动',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `first_sign_time` datetime NOT NULL COMMENT '首次签到时间',
  `restart_sign_time` datetime NOT NULL COMMENT '重新开始签到时间',
  `last_sign_time` datetime NOT NULL COMMENT '最终签到时间',
  `total_sign_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总签到次数(同天累加)',
  `total_sign_count2` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总签到次数(同天不累加)',
  `insameperiod_sign_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '同天签到次数',
  `continue_sign_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '连续签到次数',
  `is_continue_sign` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否连续签到',
  `is_do` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否完成签到',
  `lastip` char(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `valid_log_id` char(24) NOT NULL DEFAULT '' COMMENT '签到日志记录ID',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='签到-用户签到';

/*Data for the table `isign_sign` */

insert  into `isign_sign`(`_id`,`activity_id`,`user_id`,`nickname`,`headimgurl`,`first_sign_time`,`restart_sign_time`,`last_sign_time`,`total_sign_count`,`total_sign_count2`,`insameperiod_sign_count`,`continue_sign_count`,`is_continue_sign`,`is_do`,`lastip`,`valid_log_id`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('59bf74459fff638c098b4569','59bde95f9fff63070a8b4567','oFTgHwbw1xwUz8MgIBLW74kXcqnY','xxx','xxx','2017-09-18 15:22:45','2017-09-18 15:22:45','2017-09-18 15:22:45',1,1,1,1,1,0,'192.168.81.1','59bf74459fff638c098b4568','{\"xxx\":\"\"}','2017-09-18 15:22:45','2017-09-18 15:22:45',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
