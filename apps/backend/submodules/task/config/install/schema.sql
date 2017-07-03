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

/*Table structure for table `itask_log` */

DROP TABLE IF EXISTS `itask_log`;

CREATE TABLE `itask_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `task` varchar(50) NOT NULL DEFAULT '' COMMENT '任务名',
  `is_success` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否成功',
  `request` text NOT NULL COMMENT '请求参数',
  `result` text NOT NULL COMMENT '获得结果',
  `log_time` datetime NOT NULL COMMENT '日志时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='任务-日志';

/*Data for the table `itask_log` */

insert  into `itask_log`(`_id`,`task`,`is_success`,`request`,`result`,`log_time`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('569b85bb887c22054a8b462b','新一期商品生成',1,'{\"goods_commonid\":\"563728c07f50eab004000404\"}','{\"new_period\":\"1\",\"new_period_goods_id\":\"569b85bb887c22054a8b4626\"}','2016-01-17 20:14:51','2016-01-17 20:14:51','2016-01-17 20:14:51',0),('569b85bf887c22cf6c8b46d8','新一期商品生成',1,'{\"goods_commonid\":\"563728c07f50eab004000403\"}','{\"new_period\":\"1\",\"new_period_goods_id\":\"569b85bf887c22cf6c8b46d3\"}','2016-01-17 20:14:55','2016-01-17 20:14:55','2016-01-17 20:14:55',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
