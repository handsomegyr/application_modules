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

/*Table structure for table `imessage_msg` */

DROP TABLE IF EXISTS `imessage_msg`;

CREATE TABLE `imessage_msg` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '消息ID',
  `from_user_id` char(24) NOT NULL DEFAULT '' COMMENT '来自的用户ID',
  `to_user_id` char(24) NOT NULL DEFAULT '' COMMENT '发往的用户ID',
  `content` text NOT NULL COMMENT '消息内容',
  `msg_time` datetime NOT NULL COMMENT '消息时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`from_user_id`),
  KEY `NewIndex2` (`to_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息-消息';

/*Table structure for table `imessage_msg_count` */

DROP TABLE IF EXISTS `imessage_msg_count`;

CREATE TABLE `imessage_msg_count` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `user_id` char(24) NOT NULL DEFAULT '' COMMENT '用户ID',
  `sysMsgCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统消息数量',
  `privMsgCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '私信数量',
  `friendMsgCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '好友申请消息数量',
  `replyMsgCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复消息数量',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息-数量';

/*Table structure for table `imessage_msg_statistics` */

DROP TABLE IF EXISTS `imessage_msg_statistics`;

CREATE TABLE `imessage_msg_statistics` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `user1_id` char(24) NOT NULL DEFAULT '' COMMENT '用户1ID',
  `user2_id` char(24) NOT NULL DEFAULT '' COMMENT '用户2ID',
  `msg_user_id` char(24) NOT NULL DEFAULT '' COMMENT '消息发送者',
  `msg_time` datetime NOT NULL COMMENT '消息时间',
  `content` text NOT NULL COMMENT '消息内容',
  `msg_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消息数量',
  `user1_unread_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户1未读数量',
  `user2_unread_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户2未读数量',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息-消息统计';

/*Table structure for table `imessage_replymsg` */

DROP TABLE IF EXISTS `imessage_replymsg`;

CREATE TABLE `imessage_replymsg` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '回复消息ID',
  `relate_id` char(24) NOT NULL DEFAULT '' COMMENT '相关ID',
  `reply_user_id` char(24) NOT NULL DEFAULT '' COMMENT '回复用户ID',
  `reply_user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '回复用户姓名',
  `reply_user_avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '回复用户头像',
  `reply_user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '回复用户注册方式',
  `reply_content` text NOT NULL COMMENT '回复内容',
  `to_user_id` char(24) NOT NULL DEFAULT '' COMMENT '话题用户ID',
  `to_user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '话题用户姓名',
  `to_user_avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '话题用户头像',
  `to_user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '话题用户注册方式',
  `to_user_content` text NOT NULL COMMENT '话题内容',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读',
  `msg_time` datetime NOT NULL COMMENT '消息时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`reply_user_id`),
  KEY `NewIndex2` (`to_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息-回复';

/*Table structure for table `imessage_sysmsg` */

DROP TABLE IF EXISTS `imessage_sysmsg`;

CREATE TABLE `imessage_sysmsg` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '消息ID',
  `to_user_id` char(24) NOT NULL DEFAULT '' COMMENT '发往的用户ID',
  `content` text NOT NULL COMMENT '消息内容',
  `msg_time` datetime NOT NULL COMMENT '消息时间',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`to_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息-系统消息';

/*Table structure for table `imessage_template` */

DROP TABLE IF EXISTS `imessage_template`;

CREATE TABLE `imessage_template` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '模板id',
  `code` varchar(30) NOT NULL DEFAULT '' COMMENT '模板调用代码',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '模板名称',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '模板标题',
  `content` text NOT NULL COMMENT '模板内容',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='消息-模板';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
