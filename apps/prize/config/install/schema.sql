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

/*Table structure for table `iprize_category` */

DROP TABLE IF EXISTS `iprize_category`;

CREATE TABLE `iprize_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `code` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类码',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='奖品-奖品类别';

/*Table structure for table `iprize_code` */

DROP TABLE IF EXISTS `iprize_code`;

CREATE TABLE `iprize_code` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `code` char(30) NOT NULL DEFAULT '' COMMENT '虚拟卡编号',
  `pwd` char(30) NOT NULL DEFAULT '' COMMENT '虚拟卡密码',
  `is_used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用',
  `start_time` datetime NOT NULL COMMENT '开始有效期',
  `end_time` datetime NOT NULL COMMENT '结束有效期',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `index1` (`prize_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='奖品-券码';

/*Table structure for table `iprize_prize` */

DROP TABLE IF EXISTS `iprize_prize`;

CREATE TABLE `iprize_prize` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `prize_name` varchar(50) NOT NULL DEFAULT '' COMMENT '奖品名',
  `prize_code` char(24) NOT NULL DEFAULT '' COMMENT '奖品代码',
  `is_virtual` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是虚拟奖品',
  `virtual_currency` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟价值',
  `is_need_virtual_code` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否发放奖品券码',
  `is_valid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否立即生效',
  `category` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '奖品类别',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='奖品-奖品';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
