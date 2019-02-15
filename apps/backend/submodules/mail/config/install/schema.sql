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

/*Table structure for table `imail_settings` */

DROP TABLE IF EXISTS `imail_settings`;

CREATE TABLE `imail_settings` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `host` varchar(100) NOT NULL DEFAULT '' COMMENT 'SMTP服务器',
  `port` varchar(30) NOT NULL DEFAULT '' COMMENT 'SMTP端口',
  `address_from` varchar(30) NOT NULL DEFAULT '' COMMENT '发信人邮件地址',
  `name_from` varchar(30) NOT NULL DEFAULT '' COMMENT '发信人姓名',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT 'SMTP身份验证用户名',
  `password` varchar(30) NOT NULL DEFAULT '' COMMENT 'SMTP身份验证密码',
  `secure` char(3) NOT NULL DEFAULT 'tls' COMMENT '加密,tls或ssl',
  `is_auth` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否SMTP认证',
  `is_smtp` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否使用SMTP',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='邮件-邮件设置';

/*Data for the table `imail_settings` */

insert  into `imail_settings`(`_id`,`host`,`port`,`address_from`,`name_from`,`username`,`password`,`secure`,`is_auth`,`is_smtp`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('563dc5d4887c22cf498b4570','smtp.jizigou.com','25','handsomegyr@126.com','郭永荣','info@jizigou.com','123qawq123S','',1,1,'2015-11-07 17:35:16','2015-11-16 22:36:02',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
