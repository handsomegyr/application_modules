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

/*Table structure for table `ipoints_category` */

DROP TABLE IF EXISTS `ipoints_category`;

CREATE TABLE `ipoints_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '分类ID',
  `code` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分类值',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='积分-分类';

/*Table structure for table `ipoints_log` */

DROP TABLE IF EXISTS `ipoints_log`;

CREATE TABLE `ipoints_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '积分日志ID',
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '积分分类',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '积分值',
  `stage` varchar(50) NOT NULL DEFAULT '' COMMENT '操作阶段',
  `desc` varchar(100) NOT NULL DEFAULT '' COMMENT '操作描述',
  `unique_id` char(24) NOT NULL DEFAULT '' COMMENT '唯一编号',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `is_consumed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否消耗',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分-日志';

/*Table structure for table `ipoints_rule` */

DROP TABLE IF EXISTS `ipoints_rule`;

CREATE TABLE `ipoints_rule` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '积分规则ID',
  `code` char(30) NOT NULL DEFAULT '' COMMENT '规则码',
  `item` varchar(30) NOT NULL DEFAULT '' COMMENT '项目',
  `item_category` varchar(30) NOT NULL DEFAULT '' COMMENT '项目分类',
  `category` tinyint(1) NOT NULL DEFAULT '0' COMMENT '积分分类',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '获得积分',
  `memo` varchar(50) DEFAULT '' COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='积分-规则';

/*Table structure for table `ipoints_user` */

DROP TABLE IF EXISTS `ipoints_user`;

CREATE TABLE `ipoints_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '积分分类',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户姓名',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `current` int(11) NOT NULL DEFAULT '0' COMMENT '当前积分',
  `total` int(11) NOT NULL DEFAULT '0' COMMENT '获得积分总数',
  `freeze` int(11) NOT NULL DEFAULT '0' COMMENT '冻结积分',
  `consume` int(11) NOT NULL DEFAULT '0' COMMENT '消耗积分',
  `expire` int(11) NOT NULL DEFAULT '0' COMMENT '过期积分',
  `point_time` datetime NOT NULL COMMENT '变动时间',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex2` (`user_id`,`category`) USING BTREE,
  KEY `NewIndex1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分-用户';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
