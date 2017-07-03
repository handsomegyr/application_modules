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

/*Table structure for table `ilottery_exchange` */

DROP TABLE IF EXISTS `ilottery_exchange`;

CREATE TABLE `ilottery_exchange` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '抽奖ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `is_valid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效',
  `got_time` datetime NOT NULL COMMENT '获取时间',
  `source` char(10) NOT NULL DEFAULT '' COMMENT '来源',
  `prize_code` char(24) NOT NULL DEFAULT '' COMMENT '奖品信息.奖品代码',
  `prize_name` varchar(50) NOT NULL DEFAULT '' COMMENT '奖品信息.奖品名',
  `prize_category` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '奖品信息.奖品类别',
  `prize_virtual_currency` int(11) NOT NULL COMMENT '奖品信息.奖品价值',
  `prize_is_virtual` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '奖品信息.是否实物奖',
  `prize_virtual_code` char(24) NOT NULL DEFAULT '' COMMENT '券码信息.卡号',
  `prize_virtual_pwd` char(30) NOT NULL DEFAULT '' COMMENT '券码信息.卡密',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户信息.名称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户信息.头像',
  `contact_name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系信息.姓名',
  `contact_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系信息.手机',
  `contact_address` varchar(200) NOT NULL DEFAULT '' COMMENT '联系信息.地址',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽奖-中奖';

/*Data for the table `ilottery_exchange` */

insert  into `ilottery_exchange`(`_id`,`activity_id`,`user_id`,`prize_id`,`is_valid`,`got_time`,`source`,`prize_code`,`prize_name`,`prize_category`,`prize_virtual_currency`,`prize_is_virtual`,`prize_virtual_code`,`prize_virtual_pwd`,`user_name`,`user_headimgurl`,`contact_name`,`contact_mobile`,`contact_address`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5865f1edfcc2b60a008b456c','5861e812887c22015f8b456b','xxxx','569b85bf887c22cf6c8b46d4',1,'2016-12-30 13:34:37','weixin','569b85bf887c22cf6c8b46d3','优惠券1',1,10,1,'10000002','1234','xx','xx','guoyongrong','13564100096','shanghai','{\"activity_user_id\":\"5865eed5fcc2b6e8008b4568\"}','2016-12-30 13:34:37','2016-12-30 13:42:45',0);

/*Table structure for table `ilottery_limit` */

DROP TABLE IF EXISTS `ilottery_limit`;

CREATE TABLE `ilottery_limit` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '抽奖限制ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `limit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '限制数量',
  `start_time` datetime NOT NULL COMMENT '限制开始时间',
  `end_time` datetime NOT NULL COMMENT '限制结束时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽奖-限制';

/*Data for the table `ilottery_limit` */

insert  into `ilottery_limit`(`_id`,`activity_id`,`prize_id`,`limit`,`start_time`,`end_time`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5864a5c7c63b7b08008b4568','5861e812887c22015f8b456b','569b85bf887c22cf6c8b46d4',1,'2016-12-29 13:56:54','2019-12-29 13:56:54','2016-12-29 13:57:27','2016-12-29 13:57:27',0);

/*Table structure for table `ilottery_record` */

DROP TABLE IF EXISTS `ilottery_record`;

CREATE TABLE `ilottery_record` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '抽奖记录ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `source` char(10) NOT NULL DEFAULT '' COMMENT '来源',
  `result_id` smallint(6) NOT NULL DEFAULT '0' COMMENT '结果ID',
  `result_msg` text NOT NULL COMMENT '结果说明',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽奖-日志';

/*Data for the table `ilottery_record` */

insert  into `ilottery_record`(`_id`,`activity_id`,`user_id`,`source`,`result_id`,`result_msg`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5865f1edfcc2b60a008b456d','5861e812887c22015f8b456b','xxxx','weixin',1,'恭喜您中奖了！','2016-12-30 13:34:37','2016-12-30 13:34:37',0);

/*Table structure for table `ilottery_rule` */

DROP TABLE IF EXISTS `ilottery_rule`;

CREATE TABLE `ilottery_rule` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '抽奖规则ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `allow_start_time` datetime NOT NULL COMMENT '开始时间',
  `allow_end_time` datetime NOT NULL COMMENT '结束时间',
  `allow_number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '奖品数量',
  `allow_probability` int(11) NOT NULL DEFAULT '0' COMMENT '抽奖概率',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽奖-规则';

/*Data for the table `ilottery_rule` */

insert  into `ilottery_rule`(`_id`,`activity_id`,`prize_id`,`allow_start_time`,`allow_end_time`,`allow_number`,`allow_probability`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('569b85bf887c22cf6c8b46d7','5861e812887c22015f8b456b','569b85bf887c22cf6c8b46d4','2016-01-17 20:14:55','2099-12-31 23:59:59',1,10000,'2016-01-17 20:14:55','2016-12-30 13:34:37',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
