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

/*Table structure for table `ipoints_category` */

DROP TABLE IF EXISTS `ipoints_category`;

CREATE TABLE `ipoints_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '分类ID',
  `code` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分类值',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='积分-分类';

/*Data for the table `ipoints_category` */

insert  into `ipoints_category`(`_id`,`code`,`name`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56461477887c22f45e8b4570',1,'福分','2015-11-14 00:48:55','2015-11-14 00:48:55',0),('5646149f887c22f35e8b456e',2,'经验值','2015-11-14 00:49:35','2015-11-14 00:49:45',0),('56757af1887c22cf6c8b4648',3,'预付款','2015-12-19 23:42:40','2015-12-19 23:42:40',0);

/*Table structure for table `ipoints_log` */

DROP TABLE IF EXISTS `ipoints_log`;

CREATE TABLE `ipoints_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '积分日志ID',
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '积分分类',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '积分值',
  `stage` varchar(50) NOT NULL DEFAULT '' COMMENT '操作阶段',
  `desc` varchar(100) NOT NULL DEFAULT '' COMMENT '操作描述',
  `unique_id` char(24) NOT NULL DEFAULT '' COMMENT '唯一编号',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `is_consumed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否消耗',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分-日志';

/*Data for the table `ipoints_log` */

insert  into `ipoints_log`(`_id`,`category`,`user_id`,`user_name`,`user_headimgurl`,`points`,`stage`,`desc`,`unique_id`,`add_time`,`is_consumed`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('569b72a3887c2206528b4780',1,'569b72a3887c2206528b477c','13564100096','UserFace-160-0000.jpg',10,'注册','注册获得福分1','569b72a3887c2206528b477c','2016-01-17 18:53:23',0,'2016-01-17 18:53:23','2016-01-17 18:53:23',0),('569cdf08887c22774a8b57d1',1,'569cdf08887c22774a8b57cd','handsomegyr@126.com','UserFace-160-0000.jpg',10,'注册','注册获得福分1','569cdf08887c22774a8b57cd','2016-01-18 20:48:08',0,'2016-01-18 20:48:08','2016-01-18 20:48:08',0),('569e3ad3887c22024a8b46eb',1,'569b72a3887c2206528b477c','郭永荣','UserFace-160-0000.jpg',10,'完善个人资料','昵称','569e3ad3887c22024a8b46e1','2016-01-19 21:32:03',0,'2016-01-19 21:32:03','2016-01-19 21:32:03',0),('569e3ad3887c22024a8b46ec',1,'569b72a3887c2206528b477c','郭永荣','UserFace-160-0000.jpg',5,'完善个人资料','性别','569e3ad3887c22024a8b46e3','2016-01-19 21:32:03',0,'2016-01-19 21:32:03','2016-01-19 21:32:03',0),('569e3ad3887c22024a8b46ed',1,'569b72a3887c2206528b477c','郭永荣','UserFace-160-0000.jpg',5,'完善个人资料','生日','569e3ad3887c22024a8b46e4','2016-01-19 21:32:03',0,'2016-01-19 21:32:03','2016-01-19 21:32:03',0),('569e3ad3887c22024a8b46ee',1,'569b72a3887c2206528b477c','郭永荣','UserFace-160-0000.jpg',5,'完善个人资料','现居地','569e3ad3887c22024a8b46e6','2016-01-19 21:32:03',0,'2016-01-19 21:32:03','2016-01-19 21:32:03',0),('569e3ad3887c22024a8b46ef',1,'569b72a3887c2206528b477c','郭永荣','UserFace-160-0000.jpg',5,'完善个人资料','家乡','569e3ad3887c22024a8b46e7','2016-01-19 21:32:03',0,'2016-01-19 21:32:03','2016-01-19 21:32:03',0),('569e3ad4887c22024a8b46f0',1,'569b72a3887c2206528b477c','郭永荣','UserFace-160-0000.jpg',5,'完善个人资料','QQ','569e3ad3887c22024a8b46e8','2016-01-19 21:32:03',0,'2016-01-19 21:32:03','2016-01-19 21:32:03',0),('569e3ad4887c22024a8b46f1',1,'569b72a3887c2206528b477c','郭永荣','UserFace-160-0000.jpg',5,'完善个人资料','月收入','569e3ad3887c22024a8b46e9','2016-01-19 21:32:03',0,'2016-01-19 21:32:03','2016-01-19 21:32:03',0),('569e3ad4887c22024a8b46f2',1,'569b72a3887c2206528b477c','郭永荣','UserFace-160-0000.jpg',10,'完善个人资料','签名','569e3ad3887c22024a8b46ea','2016-01-19 21:32:03',0,'2016-01-19 21:32:03','2016-01-19 21:32:03',0),('56a06e62887c22cf598b45d3',1,'56a06e62887c22cf598b45cf','15821039514','UserFace-160-0000.jpg',10,'注册','注册获得福分1','56a06e62887c22cf598b45cf','2016-01-21 13:36:33',0,'2016-01-21 13:36:33','2016-01-21 13:36:33',0),('56a2f9fc887c224f7b8b456c',1,'56a2f9fc887c224f7b8b4568','137103340@qq.com','UserFace-160-0000.jpg',10,'注册','注册获得福分1','56a2f9fc887c224f7b8b4568','2016-01-23 11:56:44',0,'2016-01-23 11:56:44','2016-01-23 11:56:44',0);

/*Table structure for table `ipoints_rule` */

DROP TABLE IF EXISTS `ipoints_rule`;

CREATE TABLE `ipoints_rule` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '积分规则ID',
  `code` char(30) NOT NULL DEFAULT '' COMMENT '规则码',
  `item` varchar(30) NOT NULL DEFAULT '' COMMENT '项目',
  `item_category` varchar(30) NOT NULL DEFAULT '' COMMENT '项目分类',
  `category` tinyint(1) NOT NULL DEFAULT '0' COMMENT '积分分类',
  `points` int(11) NOT NULL DEFAULT '0' COMMENT '获得积分',
  `memo` varchar(50) DEFAULT '' COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='积分-规则';

/*Data for the table `ipoints_rule` */

insert  into `ipoints_rule`(`_id`,`code`,`item`,`item_category`,`category`,`points`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('56460a45887c22f45e8b4568','buy','参与云购每消费1元','',2,10,'生日当月享双倍福分','2015-11-14 00:05:25','2015-11-14 00:12:30',0),('56460a54887c22f35e8b4567','invitation','每成功邀请1位好友并消费','',2,50,'','2015-11-14 00:05:40','2015-11-14 00:05:40',0),('56460a99887c22f15e8b4568','suc_list','成功晒单','',2,500,'限时揭晓商品除外','2015-11-14 00:06:49','2015-11-14 00:12:53',0),('56460aaa887c22f45e8b4569','list_vote','晒单评论','',2,10,'最多100福分、1000经验值每天','2015-11-14 00:07:06','2015-11-14 00:13:11',0),('56460abb887c22f35e8b4568','pub_topic','发表话题','',2,50,'','2015-11-14 00:07:23','2015-11-14 00:07:23',0),('56460acb887c22f15e8b4569','inc_topic','话题加精','',2,50,'','2015-11-14 00:07:39','2015-11-14 00:07:39',0),('56460ada887c22f45e8b456a','reply_topic','回复话题','',2,10,'','2015-11-14 00:07:54','2015-11-14 00:07:54',0),('56460aea887c22f35e8b4569','add_friends','加好友','',2,5,'','2015-11-14 00:08:10','2015-11-14 00:08:10',0),('56460afa887c22f15e8b456a','circle_admin','圈主','',2,1000,'每月获得一次','2015-11-14 00:08:26','2015-11-14 00:13:36',0),('56460b08887c22f45e8b456b','admin','管理员','',2,500,'每月获得一次','2015-11-14 00:08:40','2015-11-14 00:13:48',0),('56460b42887c22f35e8b456a','member_mobile','手机验证','完善个人资料',1,20,'','2015-11-14 00:09:38','2015-11-14 01:25:21',0),('56460b52887c22f15e8b456b','member_email','邮箱验证','完善个人资料',1,10,'','2015-11-14 00:09:54','2015-11-14 00:09:54',0),('56460b5d887c22f45e8b456c','member_nickname','昵称','完善个人资料',1,10,'','2015-11-14 00:10:05','2015-11-14 00:10:05',0),('56460b6a887c22f35e8b456b','member_sex','性别','完善个人资料',1,5,'','2015-11-14 00:10:18','2015-11-14 00:10:18',0),('56460b76887c22f15e8b456c','member_birthday','生日','完善个人资料',1,5,'','2015-11-14 00:10:30','2015-11-14 00:10:30',0),('56460b88887c22f45e8b456d','member_location','现居地','完善个人资料',1,5,'','2015-11-14 00:10:48','2015-11-14 00:10:48',0),('56460b97887c22f35e8b456c','member_hometown','家乡','完善个人资料',1,5,'','2015-11-14 00:11:03','2015-11-14 00:11:03',0),('56460ba1887c22f15e8b456d','member_qq','QQ','完善个人资料',1,5,'','2015-11-14 00:11:13','2015-11-14 00:11:13',0),('56460bac887c22f45e8b456e','member_monthly_income','月收入','完善个人资料',1,5,'','2015-11-14 00:11:24','2015-11-14 00:11:24',0),('56460bbb887c22f35e8b456d','member_signature','签名','完善个人资料',1,10,'','2015-11-14 00:11:39','2015-11-14 00:11:39',0),('56488d97887c221b608b456d','register','注册获得福分1','注册',1,10,'注册获得福分10','2015-11-15 21:50:15','2015-11-15 22:54:04',0),('564896a5887c22f05e8b456d','list_vote','晒单评论','',1,1,'','2015-11-15 22:28:53','2015-11-15 22:28:53',0),('564896cf887c22f05e8b456e','suc_list','成功晒单','',1,400,'','2015-11-15 22:29:35','2015-11-15 22:29:35',0),('564896ed887c22f25e8b456c','invitation','每成功邀请1位好友并消费','',1,50,'','2015-11-15 22:30:05','2015-11-15 22:30:05',0),('5648970d887c22f05e8b456f','buy','参与云购每消费1元','',1,1,'','2015-11-15 22:30:37','2015-11-15 22:30:37',0);

/*Table structure for table `ipoints_user` */

DROP TABLE IF EXISTS `ipoints_user`;

CREATE TABLE `ipoints_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '积分分类',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户姓名',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `current` int(11) NOT NULL DEFAULT '0' COMMENT '当前积分',
  `total` int(11) NOT NULL DEFAULT '0' COMMENT '获得积分总数',
  `freeze` int(11) NOT NULL DEFAULT '0' COMMENT '冻结积分',
  `consume` int(11) NOT NULL DEFAULT '0' COMMENT '消耗积分',
  `expire` int(11) NOT NULL DEFAULT '0' COMMENT '过期积分',
  `point_time` datetime NOT NULL COMMENT '变动时间',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex2` (`user_id`,`category`) USING BTREE,
  KEY `NewIndex1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='积分-用户';

/*Data for the table `ipoints_user` */

insert  into `ipoints_user`(`_id`,`category`,`user_id`,`user_name`,`user_headimgurl`,`current`,`total`,`freeze`,`consume`,`expire`,`point_time`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('569b72a3887c2206528b477d',1,'569b72a3887c2206528b477c','13564100096','UserFace-160-0000.jpg',60,60,0,0,0,'2016-01-19 21:32:03','{\"member_id\":\"569b72a3887c2206528b477c\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"\",\"member_mobile\":\"13564100096\",\"member_register_by\":\"1\"}','2016-01-17 18:53:23','2016-01-19 21:32:03',0),('569b72a3887c2206528b477e',2,'569b72a3887c2206528b477c','13564100096','UserFace-160-0000.jpg',0,0,0,0,0,'2016-01-17 18:53:23','{\"member_id\":\"569b72a3887c2206528b477c\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"\",\"member_mobile\":\"13564100096\",\"member_register_by\":\"1\"}','2016-01-17 18:53:23','2016-01-17 18:53:23',0),('569b72a3887c2206528b477f',3,'569b72a3887c2206528b477c','13564100096','UserFace-160-0000.jpg',0,0,0,0,0,'2016-01-17 18:53:23','{\"member_id\":\"569b72a3887c2206528b477c\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"\",\"member_mobile\":\"13564100096\",\"member_register_by\":\"1\"}','2016-01-17 18:53:23','2016-01-17 18:53:23',0),('569cdf08887c22774a8b57ce',1,'569cdf08887c22774a8b57cd','handsomegyr@126.com','UserFace-160-0000.jpg',10,10,0,0,0,'2016-01-18 20:48:08','{\"member_id\":\"569cdf08887c22774a8b57cd\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"handsomegyr@126.com\",\"member_mobile\":\"\",\"member_register_by\":\"2\"}','2016-01-18 20:48:08','2016-01-18 20:48:08',0),('569cdf08887c22774a8b57cf',2,'569cdf08887c22774a8b57cd','handsomegyr@126.com','UserFace-160-0000.jpg',0,0,0,0,0,'2016-01-18 20:48:08','{\"member_id\":\"569cdf08887c22774a8b57cd\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"handsomegyr@126.com\",\"member_mobile\":\"\",\"member_register_by\":\"2\"}','2016-01-18 20:48:08','2016-01-18 20:48:08',0),('569cdf08887c22774a8b57d0',3,'569cdf08887c22774a8b57cd','handsomegyr@126.com','UserFace-160-0000.jpg',0,0,0,0,0,'2016-01-18 20:48:08','{\"member_id\":\"569cdf08887c22774a8b57cd\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"handsomegyr@126.com\",\"member_mobile\":\"\",\"member_register_by\":\"2\"}','2016-01-18 20:48:08','2016-01-18 20:48:08',0),('56a06e62887c22cf598b45d0',1,'56a06e62887c22cf598b45cf','15821039514','UserFace-160-0000.jpg',10,10,0,0,0,'2016-01-21 13:36:33','{\"member_id\":\"56a06e62887c22cf598b45cf\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"\",\"member_mobile\":\"15821039514\",\"member_register_by\":\"1\"}','2016-01-21 13:36:33','2016-01-21 13:36:33',0),('56a06e62887c22cf598b45d1',2,'56a06e62887c22cf598b45cf','15821039514','UserFace-160-0000.jpg',0,0,0,0,0,'2016-01-21 13:36:33','{\"member_id\":\"56a06e62887c22cf598b45cf\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"\",\"member_mobile\":\"15821039514\",\"member_register_by\":\"1\"}','2016-01-21 13:36:33','2016-01-21 13:36:33',0),('56a06e62887c22cf598b45d2',3,'56a06e62887c22cf598b45cf','15821039514','UserFace-160-0000.jpg',0,0,0,0,0,'2016-01-21 13:36:33','{\"member_id\":\"56a06e62887c22cf598b45cf\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"\",\"member_mobile\":\"15821039514\",\"member_register_by\":\"1\"}','2016-01-21 13:36:33','2016-01-21 13:36:33',0),('56a2f9fc887c224f7b8b4569',1,'56a2f9fc887c224f7b8b4568','137103340@qq.com','UserFace-160-0000.jpg',10,10,0,0,0,'2016-01-23 11:56:44','{\"member_id\":\"56a2f9fc887c224f7b8b4568\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"137103340@qq.com\",\"member_mobile\":\"\",\"member_register_by\":\"2\"}','2016-01-23 11:56:44','2016-01-23 11:56:44',0),('56a2f9fc887c224f7b8b456a',2,'56a2f9fc887c224f7b8b4568','137103340@qq.com','UserFace-160-0000.jpg',0,0,0,0,0,'2016-01-23 11:56:44','{\"member_id\":\"56a2f9fc887c224f7b8b4568\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"137103340@qq.com\",\"member_mobile\":\"\",\"member_register_by\":\"2\"}','2016-01-23 11:56:44','2016-01-23 11:56:44',0),('56a2f9fc887c224f7b8b456b',3,'56a2f9fc887c224f7b8b4568','137103340@qq.com','UserFace-160-0000.jpg',0,0,0,0,0,'2016-01-23 11:56:44','{\"member_id\":\"56a2f9fc887c224f7b8b4568\",\"member_nickname\":\"\",\"member_name\":\"\",\"member_email\":\"137103340@qq.com\",\"member_mobile\":\"\",\"member_register_by\":\"2\"}','2016-01-23 11:56:44','2016-01-23 11:56:44',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
