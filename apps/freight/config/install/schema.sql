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

/*Table structure for table `ifreight_express` */

DROP TABLE IF EXISTS `ifreight_express`;

CREATE TABLE `ifreight_express` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '公司名称',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '编号',
  `letter` char(1) NOT NULL DEFAULT '' COMMENT '首字母',
  `is_order` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否常用',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '公司网址',
  `zt_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否支持服务站配送0否1是',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运价-快递公司';

/*Data for the table `ifreight_express` */

insert  into `ifreight_express`(`_id`,`name`,`state`,`code`,`letter`,`is_order`,`url`,`zt_state`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('568a7d77887c226e6a8b5728','公司1',1,'code1','G',1,'http://www.baidu.com/',1,'2016-01-04 22:11:03','2016-01-04 22:11:03',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
