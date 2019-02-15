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

/*Table structure for table `iexchange_limit` */

DROP TABLE IF EXISTS `iexchange_limit`;

CREATE TABLE `iexchange_limit` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换限制ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换限制奖品',
  `limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '兑换限制数量',
  `start_time` datetime NOT NULL COMMENT '限制开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '限制结束时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='兑换-限制';

/*Data for the table `iexchange_limit` */

/*Table structure for table `iexchange_log` */

DROP TABLE IF EXISTS `iexchange_log`;

CREATE TABLE `iexchange_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换日志ID',
  `result_code` smallint(6) NOT NULL DEFAULT '0' COMMENT '兑换结果',
  `result_msg` text NOT NULL COMMENT '兑换结果说明',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `rule_id` char(24) NOT NULL DEFAULT '' COMMENT '规则ID',
  `quantity` int(11) NOT NULL DEFAULT '0' COMMENT '兑换数量',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '兑换积分',
  `success_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换成功ID',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='兑换-日志';

/*Data for the table `iexchange_log` */

/*Table structure for table `iexchange_rule` */

DROP TABLE IF EXISTS `iexchange_rule`;

CREATE TABLE `iexchange_rule` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换规则ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换奖品',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '可兑换数量',
  `start_time` datetime NOT NULL COMMENT '兑换开始时间',
  `end_time` datetime NOT NULL COMMENT '兑换结束时间',
  `score_category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '积分分类',
  `score` int(10) NOT NULL COMMENT '兑换所需积分',
  `exchange_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已兑换数量',
  `sort` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序[从小到大]',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='兑换-规则';

/*Data for the table `iexchange_rule` */

/*Table structure for table `iexchange_success` */

DROP TABLE IF EXISTS `iexchange_success`;

CREATE TABLE `iexchange_success` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换成功ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户编号',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `quantity` int(10) NOT NULL DEFAULT '0' COMMENT '数量',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '兑换积分',
  `is_valid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效',
  `exchange_time` datetime NOT NULL COMMENT '兑换时间',
  `rule_id` char(24) NOT NULL DEFAULT '' COMMENT '兑奖规则ID',
  `prize_code` char(24) NOT NULL DEFAULT '' COMMENT '奖品信息.奖品代码',
  `prize_name` varchar(50) NOT NULL DEFAULT '' COMMENT '奖品信息.奖品名字',
  `prize_category` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '奖品信息.奖品类别',
  `prize_is_virtual` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '奖品信息.是否实物奖',
  `prize_virtual_currency` int(11) NOT NULL DEFAULT '0' COMMENT '奖品信息.奖品价值',
  `prize_virtual_code` char(24) NOT NULL DEFAULT '' COMMENT '券码信息.卡号',
  `prize_virtual_pwd` char(30) NOT NULL DEFAULT '' COMMENT '券码信息.密码',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户信息.姓名',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户信息.头像',
  `contact_name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系信息.姓名',
  `contact_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系信息.手机号',
  `contact_address` varchar(300) NOT NULL DEFAULT '' COMMENT '联系信息.地址',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`prize_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='兑换-成功记录';

/*Data for the table `iexchange_success` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
