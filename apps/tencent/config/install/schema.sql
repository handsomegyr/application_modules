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

/*Table structure for table `itencent_appkey` */

DROP TABLE IF EXISTS `itencent_appkey`;

CREATE TABLE `itencent_appkey` (
  `_id` char(24) NOT NULL DEFAULT '',
  `appName` varchar(30) NOT NULL DEFAULT '',
  `akey` char(50) NOT NULL DEFAULT '',
  `skey` char(50) NOT NULL DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `itencent_appkey` */

insert  into `itencent_appkey`(`_id`,`appName`,`akey`,`skey`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('563b1a72bbcb2674078b4567','测试应用1','101266338','b90b1d5d02d5faaa9233b7febb388153','2015-11-05 16:59:30','2015-11-05 17:26:44',0);

/*Table structure for table `itencent_application` */

DROP TABLE IF EXISTS `itencent_application`;

CREATE TABLE `itencent_application` (
  `_id` char(24) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '应用名',
  `appKeyId` char(50) NOT NULL DEFAULT '' COMMENT '应用密钥',
  `secretKey` char(50) NOT NULL DEFAULT '' COMMENT '秘钥',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='腾讯-应用设置';

/*Data for the table `itencent_application` */

insert  into `itencent_application`(`_id`,`name`,`appKeyId`,`secretKey`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('563b20c5bbcb2605038b4568','测试用','563b1a72bbcb2674078b4567','12345667890','2015-11-05 17:26:29','2015-11-05 17:26:29',0);

/*Table structure for table `itencent_oauthinfo` */

DROP TABLE IF EXISTS `itencent_oauthinfo`;

CREATE TABLE `itencent_oauthinfo` (
  `_id` char(24) NOT NULL DEFAULT '',
  `applicationId` char(24) NOT NULL DEFAULT '' COMMENT '应用',
  `access_token` char(150) NOT NULL DEFAULT '' COMMENT 'AccessToken',
  `expire_in` int(11) NOT NULL DEFAULT '0' COMMENT '生命周期',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='腾讯-授权信息';

/*Data for the table `itencent_oauthinfo` */

/*Table structure for table `itencent_user` */

DROP TABLE IF EXISTS `itencent_user`;

CREATE TABLE `itencent_user` (
  `_id` char(24) NOT NULL DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='腾讯-用户';

/*Data for the table `itencent_user` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
