/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.1.73 : Database - webcms
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

/*Table structure for table `iActivity_Activity` */

DROP TABLE IF EXISTS `iActivity_Activity`;

CREATE TABLE `iActivity_Activity` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请活动',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户微信号',
  `url` varchar(300) NOT NULL DEFAULT '' COMMENT '邀请函URL',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '头像',
  `desc` text NOT NULL COMMENT '说明',
  `worth` int(10) NOT NULL DEFAULT '0' COMMENT '价值',
  `worth2` int(11) NOT NULL DEFAULT '0' COMMENT '价值2',
  `invited_num` int(11) NOT NULL DEFAULT '0' COMMENT '接受邀请次数',
  `invited_total` int(11) DEFAULT '0' COMMENT '邀请总次数限制，0为无限制',
  `send_time` datetime DEFAULT NULL COMMENT '发送时间',
  `lock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否LOCK',
  `expire` datetime NOT NULL COMMENT '锁过期时间',
  `is_need_subscribed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否需要关注',
  `subscibe_hint_url` varchar(300) NOT NULL DEFAULT '' COMMENT '关注提示页面链接',
  `personal_receive_num` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '每人领取次数限制，0为无限制',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请-发起邀请记录';

/*Table structure for table `iActivity_Activitygotdetail` */

DROP TABLE IF EXISTS `iActivity_Activitygotdetail`;

CREATE TABLE `iActivity_Activitygotdetail` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请活动',
  `Activity_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请ID',
  `owner_user_id` char(50) NOT NULL DEFAULT '' COMMENT '发送邀请的用户ID',
  `owner_user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '发送邀请的用户名称',
  `owner_user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '发送邀请的用户头像',
  `got_user_id` char(50) NOT NULL DEFAULT '' COMMENT '接受邀请的用户ID',
  `got_user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '接受邀请的用户名称',
  `got_user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '接受邀请的用户头像',
  `got_time` datetime NOT NULL COMMENT '接受时间',
  `got_worth` int(11) NOT NULL DEFAULT '0' COMMENT '获取价值',
  `got_worth2` int(11) NOT NULL DEFAULT '0' COMMENT '获取价值2',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`Activity_id`),
  KEY `NewIndex2` (`got_user_id`,`activity_id`),
  KEY `NewIndex3` (`owner_user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请-接受邀请记录';

/*Table structure for table `iActivity_rule` */

DROP TABLE IF EXISTS `iActivity_rule`;

CREATE TABLE `iActivity_rule` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请规则ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `worth` int(11) NOT NULL DEFAULT '0' COMMENT '价值',
  `probability` int(11) NOT NULL DEFAULT '0' COMMENT '概率(N/10000)',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请-规则';

/*Table structure for table `iActivity_user` */

DROP TABLE IF EXISTS `iActivity_user`;

CREATE TABLE `iActivity_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `worth` int(10) NOT NULL DEFAULT '0' COMMENT '价值',
  `worth2` int(11) NOT NULL DEFAULT '0' COMMENT '价值2',
  `log_time` datetime NOT NULL COMMENT '记录时间',
  `lock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否LOCK',
  `expire` datetime NOT NULL COMMENT '锁过期时间',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请-邀请用戶';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
