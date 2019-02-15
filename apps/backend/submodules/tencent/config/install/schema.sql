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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `itencent_appkey` */

insert  into `itencent_appkey`(`_id`,`appName`,`akey`,`skey`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('563b1a72bbcb2674078b4567','集资购','101327614','1018d1c75bb1e3833a0159925a79d7ce','2015-11-05 16:59:30','2015-11-05 17:26:44',0);

/*Table structure for table `itencent_application` */

DROP TABLE IF EXISTS `itencent_application`;

CREATE TABLE `itencent_application` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '应用名',
  `akey` char(50) NOT NULL DEFAULT '' COMMENT 'APP ID',
  `skey` char(50) NOT NULL DEFAULT '' COMMENT 'APP KEY',
  `secretKey` char(50) NOT NULL DEFAULT '' COMMENT '秘钥',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='腾讯-应用设置';

/*Data for the table `itencent_application` */

insert  into `itencent_application`(`_id`,`name`,`akey`,`skey`,`secretKey`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('563b20c5bbcb2605038b4568','集资购','101327614','1018d1c75bb1e3833a0159925a79d7ce','12345667890','2015-11-05 17:26:29','2015-11-05 17:26:29',0);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='腾讯-授权信息';

/*Data for the table `itencent_oauthinfo` */

/*Table structure for table `itencent_user` */

DROP TABLE IF EXISTS `itencent_user`;

CREATE TABLE `itencent_user` (
  `_id` char(24) NOT NULL DEFAULT '',
  `openid` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '头像',
  `year` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '出生年',
  `gender` varchar(4) NOT NULL DEFAULT '' COMMENT '性别',
  `province` varchar(20) NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(20) NOT NULL DEFAULT '' COMMENT '市',
  `is_yellow_vip` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为黄钻用户',
  `vip` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为黄钻用户',
  `yellow_vip_level` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '黄钻等级',
  `level` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '黄钻等级',
  `is_yellow_year_vip` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '否为年费黄钻用户',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='腾讯-用户';

/*Data for the table `itencent_user` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
