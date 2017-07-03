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

/*Table structure for table `iinvitation_invitation` */

DROP TABLE IF EXISTS `iinvitation_invitation`;

CREATE TABLE `iinvitation_invitation` (
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

/*Data for the table `iinvitation_invitation` */

insert  into `iinvitation_invitation`(`_id`,`activity_id`,`user_id`,`url`,`user_name`,`user_headimgurl`,`desc`,`worth`,`worth2`,`invited_num`,`invited_total`,`send_time`,`lock`,`expire`,`is_need_subscribed`,`subscibe_hint_url`,`personal_receive_num`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('569b72a3887c2206528b4782','565d5aaa7f50ea081300002d','569b72a3887c2206528b477c','','13564100096','UserFace-160-0000.jpg','云购',0,0,1,0,'2016-01-17 18:53:23',0,'2016-01-17 18:53:23',0,'',1,'{\"member_id\":\"569b72a3887c2206528b477c\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"\",\"member_mobile\":\"13564100096\",\"member_register_by\":\"1\",\"invitation_user_id\":\"569b72a3887c2206528b4781\"}','2016-01-17 18:53:23','2016-01-18 20:48:08',0),('569cdf08887c22774a8b57d3','565d5aaa7f50ea081300002d','569cdf08887c22774a8b57cd','','handsomegyr@126.com','UserFace-160-0000.jpg','云购',0,0,0,0,'2016-01-18 20:48:08',0,'2016-01-18 20:48:08',0,'',1,'{\"member_id\":\"569cdf08887c22774a8b57cd\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"handsomegyr@126.com\",\"member_mobile\":\"\",\"member_register_by\":\"2\",\"invitation_user_id\":\"569cdf08887c22774a8b57d2\"}','2016-01-18 20:48:08','2016-01-18 20:48:08',0),('56a06e62887c22cf598b45d5','565d5aaa7f50ea081300002d','56a06e62887c22cf598b45cf','','15821039514','UserFace-160-0000.jpg','云购',0,0,0,0,'2016-01-21 13:36:33',0,'2016-01-21 13:36:33',0,'',1,'{\"member_id\":\"56a06e62887c22cf598b45cf\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"\",\"member_mobile\":\"15821039514\",\"member_register_by\":\"1\",\"invitation_user_id\":\"56a06e62887c22cf598b45d4\"}','2016-01-21 13:36:33','2016-01-21 13:36:33',0),('56a2f9fc887c224f7b8b456e','565d5aaa7f50ea081300002d','56a2f9fc887c224f7b8b4568','','137103340@qq.com','UserFace-160-0000.jpg','云购',0,0,0,0,'2016-01-23 11:56:44',0,'2016-01-23 11:56:44',0,'',1,'{\"member_id\":\"56a2f9fc887c224f7b8b4568\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"137103340@qq.com\",\"member_mobile\":\"\",\"member_register_by\":\"2\",\"invitation_user_id\":\"56a2f9fc887c224f7b8b456d\"}','2016-01-23 11:56:44','2016-01-23 11:56:44',0);

/*Table structure for table `iinvitation_invitationgotdetail` */

DROP TABLE IF EXISTS `iinvitation_invitationgotdetail`;

CREATE TABLE `iinvitation_invitationgotdetail` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请活动',
  `invitation_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请ID',
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
  KEY `NewIndex1` (`invitation_id`),
  KEY `NewIndex2` (`got_user_id`,`activity_id`),
  KEY `NewIndex3` (`owner_user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请-接受邀请记录';

/*Data for the table `iinvitation_invitationgotdetail` */

insert  into `iinvitation_invitationgotdetail`(`_id`,`activity_id`,`invitation_id`,`owner_user_id`,`owner_user_name`,`owner_user_headimgurl`,`got_user_id`,`got_user_name`,`got_user_headimgurl`,`got_time`,`got_worth`,`got_worth2`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('569cdf08887c22774a8b57d5','565d5aaa7f50ea081300002d','569b72a3887c2206528b4782','569b72a3887c2206528b477c','13564100096','UserFace-160-0000.jpg','569cdf08887c22774a8b57cd','handsomegyr@126.com','UserFace-160-0000.jpg','2016-01-18 20:48:08',0,0,'{\"rand\":0}','2016-01-18 20:48:08','2016-01-18 20:48:08',0);

/*Table structure for table `iinvitation_rule` */

DROP TABLE IF EXISTS `iinvitation_rule`;

CREATE TABLE `iinvitation_rule` (
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

/*Data for the table `iinvitation_rule` */

/*Table structure for table `iinvitation_user` */

DROP TABLE IF EXISTS `iinvitation_user`;

CREATE TABLE `iinvitation_user` (
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

/*Data for the table `iinvitation_user` */

insert  into `iinvitation_user`(`_id`,`activity_id`,`user_id`,`user_name`,`user_headimgurl`,`worth`,`worth2`,`log_time`,`lock`,`expire`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('569b72a3887c2206528b4781','565d5aaa7f50ea081300002d','569b72a3887c2206528b477c','13564100096','UserFace-160-0000.jpg',0,0,'2016-01-17 18:53:23',0,'2016-01-17 18:53:23','{\"member_id\":\"569b72a3887c2206528b477c\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"\",\"member_mobile\":\"13564100096\",\"member_register_by\":\"1\"}','2016-01-17 18:53:23','2016-01-17 18:53:23',0),('569cdf08887c22774a8b57d2','565d5aaa7f50ea081300002d','569cdf08887c22774a8b57cd','handsomegyr@126.com','UserFace-160-0000.jpg',0,0,'2016-01-18 20:48:08',0,'2016-01-18 20:48:08','{\"member_id\":\"569cdf08887c22774a8b57cd\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"handsomegyr@126.com\",\"member_mobile\":\"\",\"member_register_by\":\"2\"}','2016-01-18 20:48:08','2016-01-18 20:48:08',0),('56a06e62887c22cf598b45d4','565d5aaa7f50ea081300002d','56a06e62887c22cf598b45cf','15821039514','UserFace-160-0000.jpg',0,0,'2016-01-21 13:36:33',0,'2016-01-21 13:36:33','{\"member_id\":\"56a06e62887c22cf598b45cf\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"\",\"member_mobile\":\"15821039514\",\"member_register_by\":\"1\"}','2016-01-21 13:36:33','2016-01-21 13:36:33',0),('56a2f9fc887c224f7b8b456d','565d5aaa7f50ea081300002d','56a2f9fc887c224f7b8b4568','137103340@qq.com','UserFace-160-0000.jpg',0,0,'2016-01-23 11:56:44',0,'2016-01-23 11:56:44','{\"member_id\":\"56a2f9fc887c224f7b8b4568\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"137103340@qq.com\",\"member_mobile\":\"\",\"member_register_by\":\"2\"}','2016-01-23 11:56:44','2016-01-23 11:56:44',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
