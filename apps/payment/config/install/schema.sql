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

/*Table structure for table `ipayment_log` */

DROP TABLE IF EXISTS `ipayment_log`;

CREATE TABLE `ipayment_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '日志ID',
  `user_id` char(24) NOT NULL DEFAULT '' COMMENT '用户ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付类型 0:全部 1:充值 2:消费 3:转账',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `log_time` datetime NOT NULL COMMENT '记录时间',
  `desc` varchar(50) NOT NULL DEFAULT '' COMMENT '支付说明',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付-日志';

/*Data for the table `ipayment_log` */

/*Table structure for table `ipayment_notify` */

DROP TABLE IF EXISTS `ipayment_notify`;

CREATE TABLE `ipayment_notify` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '通知ID',
  `out_trade_no` char(24) NOT NULL DEFAULT '' COMMENT '支付单号',
  `content` text NOT NULL COMMENT '通知内容',
  `notify_time` datetime NOT NULL COMMENT '通知时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`out_trade_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付-通知';

/*Data for the table `ipayment_notify` */

/*Table structure for table `ipayment_payment` */

DROP TABLE IF EXISTS `ipayment_payment`;

CREATE TABLE `ipayment_payment` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '支付索引id',
  `code` char(20) NOT NULL DEFAULT '' COMMENT '支付代码名称',
  `name` char(20) NOT NULL DEFAULT '' COMMENT '支付名称',
  `config` text NOT NULL COMMENT '支付接口配置信息',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '接口状态0禁用1启用',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付方式表';

/*Data for the table `ipayment_payment` */

insert  into `ipayment_payment`(`_id`,`code`,`name`,`config`,`state`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('563de1dd887c221b1c8b4568','alipay_dir','支付宝','{\"partner\":\"2088901019915360\",\"key\":\"5dyp5calr6d3ufcrnfx3nq3aqaw6rouz\",\"seller_email\":\"alva@catholic.net.cn\"}',1,'2015-11-07 19:34:53','2016-01-19 20:47:44',0),('563de203887c22d1498b4571','tenpay','财付通','{\"tenpay_account\":\"1235094901\",\"tenpay_key\":\"d2bdc43ec779b6634a3240fbed213bd3\"}',1,'2015-11-07 19:35:31','2015-11-07 19:41:53',0),('563de224887c22d2498b456f','chinabank','网银在线','{\"chinabank_account\":\"23099141\",\"chinabank_key\":\"io0uxhxbgc65j5p360y8ofx2t9ynzuv2\"}',0,'2015-11-07 19:36:04','2015-11-07 19:42:42',0),('563de247887c22fa188b4568','weixinpay','微信支付','{\"appId\":\"\",\"appSecret\":\"\",\"mchid\":\"\",\"sub_mch_id\":\"\",\"key\":\"\"}',1,'2015-11-07 19:36:39','2016-01-19 21:19:26',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
