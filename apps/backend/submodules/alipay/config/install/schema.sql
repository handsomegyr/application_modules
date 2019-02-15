/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.6.35-log : Database - webcms
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

/*Table structure for table `ialipay_application` */

DROP TABLE IF EXISTS `ialipay_application`;

CREATE TABLE `ialipay_application` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `app_id` char(32) NOT NULL DEFAULT '' COMMENT '应用ID',
  `app_name` varchar(50) NOT NULL DEFAULT '' COMMENT '应用名称',
  `merchant_private_key` text NOT NULL COMMENT '商户私钥',
  `merchant_public_key` text NOT NULL COMMENT '商户应用公钥',
  `alipay_public_key` text NOT NULL COMMENT '支付宝公钥',
  `charset` char(10) NOT NULL DEFAULT '' COMMENT '编码格式',
  `gatewayUrl` varchar(100) NOT NULL DEFAULT '' COMMENT '支付宝网关',
  `sign_type` char(10) NOT NULL DEFAULT '' COMMENT '签名方式',
  `secretKey` char(50) NOT NULL DEFAULT '' COMMENT '签名密钥',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='支付宝-应用设置';

/*Data for the table `ialipay_application` */

insert  into `ialipay_application`(`_id`,`app_id`,`app_name`,`merchant_private_key`,`merchant_public_key`,`alipay_public_key`,`charset`,`gatewayUrl`,`sign_type`,`secretKey`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('59ed5c149fff63120a8b456a','2017071707783020','华安基金财富号','MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCebQNO/vygYL0xYg7nSIwbu6gvr9c WZW4SU8V4mJtOBG1XFNe3RlgO0zGuVUzNndnVhloW2a1IGUlW0D6NEUYbhVLds7FB1u7N9h993L6TtBvxvzokC81fcIMGMuzxs1p2JiZCp4pYZbwlBGuPjRbwL df7uiUwUT8tzciUknrVsy2lsoR/oJHMX2ZyyXmT5Dviu3v6801iIFB40X3oxxph59w9RHjOisY52ihEGsITsPihIVZSAs pzoTgZrfR/56zfBzagVY97DCiZsAvekKmrDiPxWyY035MlPoDwoR9S IYFmOoFWbBOQVMhrU qR/9ik5kbURgSfgg9SyXiJAgMBAAECggEAeyWj/8DnoMbx6bQef5v BQS /KqD9xxt7D1X81vxRJLj QUWbVQSiKIv0P15hACfmjzsLRl3Ye4XkY04mgUNfKr9dJaarb9Fh cmKuyxfG7NSUX1i9WgTMU/lGWl2k37mH5cSAJf5SzNxp5ii/4gdv4CNKx4rvo3pUUQd4Fj0ymNzZ 6ffl8K7AIQsos0uLGjYCpu0S sO80SI7eIuz/4bLdNQNxtseaCmXDI3IYGcZ86S235ii8TpRWYyd2XJZx1xsIdUM3XN9ztPF3Q8zvclUm 9m5gnLZX5e2/PD7DQS6JM7N/RCjG/U8e7mlIu8MavMA0IiCJYvw8cSLwk3KbQKBgQDP9DPVuIQ2F6z00hPBRO7LuCAz u/ZoK1xdOUBOIgoxLf3CO36zSDv HndKDeuku56hHVOW6/ZoDpiZ1YIPw3PVkU7YrSGIkwrEymfLPmDfQe80cZaEouEKUJfenU hTlh6W2kfE zw0/Q3VvUAW85AHdZFj6A2JWgpJ6n881rBwKBgQDDB2KqQLvXA1IOzqx6axsBVzuoeFIX5KnfPlf4mBv3kcKbIz4W0KIHWzlRjg6gPCpzuCYxD1HVh sdLJIiTf/QK/FyYJjqeHzzxY9esPueN7nKmSMLxnqw07u7i2housBhgZh/IO4iRZWjOUvL15P2LYoG8bNu eUDIV52v7zL7wKBgAQsLzoTmLuJIBRNfs369xupVyQT3eos i3znYC3xKukvtg7GqNUqFuITdGtM jR0 0raTsoRdAFEgbVcfl6YmmNnpgBdAPY/lRC7yvdeCg2Qwh74RH/m0MMnONTjrSxcaRQTb3mLKc7vQdA2c3YNYYcR0jHVHu7XM07YF YWb/TAoGBALVq0lHzI4hv804I3JKHvTgzEDC3tA27zHT5BlYeEK3rre4oC2KnsoFLT3dYwKw3K3o3i3dJe M4qvGZe1pj2hmoNtIhnz1E1V7hoGsCER1tUutuJhaXC5wfaVuc44PAbKKHLfbcCAhjWD3cV3CH2YA/fkzVWwdZd7nAuaGW3CWLAoGAeTH3ayY3FMR0cNn2RqShdU1jvzSoigE/tW5G8aDYBbKdJfVzMlkfpv6xoOmRh5ULpHP0wjVb8FQUgTUDITonUhlK09ibuudXIITj2ANeN0rQRLKTp9kf9/QUgMOtaHeioozJdeX9I86lqn06LI0xcN743LtirPsiTHE8NIqvDhA=','MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnm0DTv78oGC9MWIO50iMG7uoL6/XPlmVuElPFeJibTgRtVxTXt0ZYDtMxrlVMzZ3Z1YZaFtmtSBlJVtA jRFGG4VS3bOxQdbuzfYffdy k7Qb8b86JAvNX3CDBjLs8bNadiYmQqeKWGW8JQRrj40W8C/nX 7olMFE/Lc3IlJJ61bMtpbKEf6CRzF9mcsl5k Q74rt7 vNNYiBQeNF96McaYefcPUR4zorGOdooRBrCE7D4oSFWUgLPqc6E4Ga30f es3wc2oFWPewwombAL3pCpqw4j8VsmNN TJT6A8KEfUviGBZjqBVmwTkFTIa1Pqkf/YpOZG1EYEn4IPUsl4iQIDAQAB','MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsaSbmpsasfWXi4mvFZQAH7uyRFrFib0KE2PwxWsVdCkxBG3D49nNvSUwoZaT2jYNYD2QPMTZOqDa7/1t3aphn3iGRSvF5Wu3PsEF4E5BO0F6qH kF/3nL8/OE2q0SVblDlXFUDMCV2DKSSW3myuFfCJSPO VOi2W5hEaK4B kdPz2OuUwczMVBZhu7aXkPNNz1txPHmbClYoJohKFRPJnygjSdm9JhWhAETakSCwN9kWvl7 keBgzN0OUkCy1g 1V giZvcVSm1J45Uz41PHV6BCAmUVnmMRCWhFhMCs0VEhQJL9t4Jg E1x1PJhKiNXoOJ38D QbtcIo5bDAP6syQIDAQAB','utf-8','https://openapi.alipay.com/gateway.do','RSA2','170908fg0353','2017-10-23 11:03:48','2017-10-23 11:20:09',0);

/*Table structure for table `ialipay_callbackurls` */

DROP TABLE IF EXISTS `ialipay_callbackurls`;

CREATE TABLE `ialipay_callbackurls` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `app_id` char(32) NOT NULL DEFAULT '' COMMENT '应用ID',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '回调地址安全域名',
  `is_valid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='支付宝-回调地址安全域名';

/*Data for the table `ialipay_callbackurls` */

insert  into `ialipay_callbackurls`(`_id`,`app_id`,`url`,`is_valid`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('59ed63f49fff63120a8b456b','2017071707783020','http://www.baidu.com/',1,'2017-10-23 11:37:24','2017-10-23 11:37:24',0);

/*Table structure for table `ialipay_script_tracking` */

DROP TABLE IF EXISTS `ialipay_script_tracking`;

CREATE TABLE `ialipay_script_tracking` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `app_id` char(32) NOT NULL DEFAULT '' COMMENT '应用ID',
  `type` char(10) NOT NULL DEFAULT '' COMMENT '监控类型',
  `start_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '截止时间',
  `execute_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '执行计算',
  `who` char(30) NOT NULL DEFAULT '' COMMENT 'who',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='支付宝-授权执行时间跟踪统计';

/*Data for the table `ialipay_script_tracking` */

insert  into `ialipay_script_tracking`(`_id`,`app_id`,`type`,`start_time`,`end_time`,`execute_time`,`who`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('59ed668f9fff630f0a8b4568','2017071707783020','test',100,200,100,'test','2017-10-23 11:48:31','2017-10-23 11:48:31',0);

/*Table structure for table `ialipay_user` */

DROP TABLE IF EXISTS `ialipay_user`;

CREATE TABLE `ialipay_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `app_id` char(32) NOT NULL DEFAULT '' COMMENT '应用ID',
  `user_id` char(16) NOT NULL DEFAULT '' COMMENT '用户ID',
  `nick_name` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `avatar` varchar(400) NOT NULL DEFAULT '' COMMENT '头像',
  `province` varchar(20) NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(20) NOT NULL DEFAULT '' COMMENT '市',
  `is_student_certified` char(1) NOT NULL DEFAULT '' COMMENT '是否是学生(T/F)',
  `user_type` char(1) NOT NULL DEFAULT '' COMMENT '用户类型（1/2）',
  `user_status` char(1) NOT NULL DEFAULT '' COMMENT '用户状态（Q/T/B/W）',
  `is_certified` char(1) NOT NULL DEFAULT '' COMMENT '是否通过实名认证(T/F)',
  `gender` char(1) NOT NULL DEFAULT '' COMMENT '性别(F/M)',
  `access_token` text NOT NULL COMMENT 'Access Token',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='支付宝-用户';

/*Data for the table `ialipay_user` */

insert  into `ialipay_user`(`_id`,`app_id`,`user_id`,`nick_name`,`avatar`,`province`,`city`,`is_student_certified`,`user_type`,`user_status`,`is_certified`,`gender`,`access_token`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('59ed66f19fff63120a8b456c','2017071707783020','2088602345138428','永荣','https://tfs.alipayobjects.com/images/partner/T1VpheXnCdXXXXXXXX','上海','上海市','F','2','T','T','m','{\"access_token\":\"authusrB108f4fbe01ba4339bdbde0d5cf1ceX42\",\"alipay_user_id\":\"20880054982402656378201281018242\",\"expires_in\":600,\"re_expires_in\":2592000,\"refresh_token\":\"authusrB9a1f72aa5df34cd9adc1dd5742677X42\",\"user_id\":\"2088602345138428\"}','2017-10-23 11:50:09','2017-10-23 11:50:09',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
