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

/*Table structure for table `imember_consignee` */

DROP TABLE IF EXISTS `imember_consignee`;

CREATE TABLE `imember_consignee` (
  `_id` char(24) NOT NULL DEFAULT '',
  `member_id` char(24) NOT NULL DEFAULT '' COMMENT '会员ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `province` int(11) NOT NULL DEFAULT '0' COMMENT '省份',
  `city` int(11) NOT NULL DEFAULT '0' COMMENT '城市',
  `district` int(11) NOT NULL DEFAULT '0' COMMENT '地区',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '详细地址',
  `zipcode` int(11) NOT NULL DEFAULT '0' COMMENT '邮政编码',
  `telephone` varchar(20) NOT NULL DEFAULT '' COMMENT '电话',
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1默认收货地址',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='收货地址表';

/*Data for the table `imember_consignee` */

insert  into `imember_consignee`(`_id`,`member_id`,`name`,`province`,`city`,`district`,`address`,`zipcode`,`telephone`,`mobile`,`is_default`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('569f8ee7887c226e6a8b5758','569b72a3887c2206528b477c','郭永荣',310000,310100,310110,'延吉中路25弄5号楼902室',200093,'123123','13564100096',0,'2016-01-20 21:43:03','2016-01-23 20:33:08',0),('569f90a8887c22024a8b46f3','569b72a3887c2206528b477c','郭永荣',310000,310200,310230,'新民7对',200056,'59370385','18917659157',0,'2016-01-20 21:50:32','2016-01-23 20:33:08',0),('56a072f9887c22184e8b4654','56a06e62887c22cf598b45cf','12312',310000,310100,310101,'123123',123123,'1111','123123',1,'2016-01-21 13:56:09','2016-01-23 20:33:08',0);

/*Table structure for table `imember_friend` */

DROP TABLE IF EXISTS `imember_friend`;

CREATE TABLE `imember_friend` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `from_user_id` char(24) NOT NULL DEFAULT '' COMMENT '发起用户ID',
  `from_user_nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '发起用户名称',
  `from_user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '发起用户邮箱',
  `from_user_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '发起用户手机',
  `from_user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发起用户注册方式',
  `to_user_id` char(24) NOT NULL DEFAULT '' COMMENT '接受用户ID',
  `to_user_nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '接受用户名称',
  `to_user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '接受用户邮箱',
  `to_user_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '接受用户手机',
  `to_user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '接受用户注册方式',
  `apply_time` datetime NOT NULL COMMENT '申请时间',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0:申请中 1 好友',
  `agree_time` datetime NOT NULL COMMENT '同意时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`from_user_id`),
  KEY `NewIndex2` (`to_user_id`),
  KEY `NewIndex3` (`from_user_id`,`to_user_id`),
  KEY `NewIndex4` (`to_user_id`,`from_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员-好友';

/*Data for the table `imember_friend` */

/*Table structure for table `imember_grade` */

DROP TABLE IF EXISTS `imember_grade`;

CREATE TABLE `imember_grade` (
  `_id` char(24) NOT NULL DEFAULT '',
  `level` tinyint(4) NOT NULL DEFAULT '1' COMMENT '等级',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '等级名',
  `exp_from` int(11) NOT NULL DEFAULT '0' COMMENT '经验值从',
  `exp_to` int(11) NOT NULL DEFAULT '0' COMMENT '经验值至',
  `memo` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='用户等级表';

/*Data for the table `imember_grade` */

insert  into `imember_grade`(`_id`,`level`,`name`,`exp_from`,`exp_to`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('564605bd887c22f05e8b4568',1,'云购小将',0,10000,'','2015-11-13 23:46:05','2015-11-13 23:46:05',0),('564605d9887c22f25e8b456a',2,'云购少将',10000,50000,'','2015-11-13 23:46:33','2015-11-13 23:46:33',0),('564606ec887c221b608b456a',3,'云购中将',50000,200000,'','2015-11-13 23:51:08','2015-11-13 23:51:08',0),('5646070d887c22f05e8b4569',4,'云购上将',200000,500000,'','2015-11-13 23:51:41','2015-11-13 23:51:41',0),('5646072e887c22f15e8b4567',5,'云购大将',500000,1000000,'','2015-11-13 23:52:14','2015-11-13 23:52:14',0),('5646074b887c22f45e8b4567',6,'云购将军',1000000,9999999,'','2015-11-13 23:52:43','2015-11-13 23:52:43',0);

/*Table structure for table `imember_member` */

DROP TABLE IF EXISTS `imember_member`;

CREATE TABLE `imember_member` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '会员id',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '会员邮箱',
  `email_bind` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未绑定1已绑定',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `mobile_bind` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未绑定1已绑定',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会员的开启状态 1为开启 0为关闭',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '会员昵称',
  `avatar` varchar(50) NOT NULL DEFAULT '' COMMENT '会员头像',
  `passwd` varchar(32) NOT NULL DEFAULT '' COMMENT '会员密码',
  `paypwd` char(32) NOT NULL DEFAULT '' COMMENT '支付密码',
  `tel_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '备用电话,手机或座机',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会员性别,1男,2女,0未知',
  `birthday` char(10) NOT NULL DEFAULT '' COMMENT '生日',
  `constellation` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '星座',
  `location` char(74) NOT NULL DEFAULT '' COMMENT '所在地',
  `hometown` char(74) NOT NULL DEFAULT '' COMMENT '家乡',
  `qq` varchar(100) NOT NULL DEFAULT '' COMMENT 'qq',
  `monthly_income` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '月收入',
  `signature` varchar(500) NOT NULL DEFAULT '' COMMENT '签名',
  `privacy` text NOT NULL COMMENT '隐私设定',
  `noticesettings` text NOT NULL COMMENT '常用设置',
  `inviter_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请人ID',
  `is_login_tip` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '登录保护',
  `is_smallmoney_open` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '小额免密码设置',
  `smallmoney` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '小额金额',
  `register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '会员的注册方式 1为手机 2为邮箱 3为账户',
  `ww` varchar(100) NOT NULL DEFAULT '' COMMENT '阿里旺旺',
  `quicklink` varchar(255) NOT NULL DEFAULT '' COMMENT '会员常用操作',
  `buy_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '购买次数',
  `prized_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '获得商品次数',
  `login_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_time` datetime NOT NULL COMMENT '会员注册时间',
  `login_time` datetime NOT NULL COMMENT '当前登录时间',
  `old_login_time` datetime NOT NULL COMMENT '上次登录时间',
  `login_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '当前登录ip',
  `old_login_ip` varchar(20) NOT NULL DEFAULT '' COMMENT '上次登录ip',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '会员名称',
  `truename` varchar(20) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `qqopenid` varchar(100) NOT NULL DEFAULT '' COMMENT 'qq互联id',
  `qqinfo` text COMMENT 'qq账号相关信息',
  `sinaopenid` varchar(100) NOT NULL DEFAULT '' COMMENT '新浪微博登录id',
  `sinainfo` text COMMENT '新浪账号相关信息序列化值',
  `weixinopenid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信openid',
  `weixininfo` text NOT NULL COMMENT '微信账号相关信息',
  `points` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员积分',
  `available_predeposit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '预存款可用金额',
  `freeze_predeposit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '预存款冻结金额',
  `available_rc_balance` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '可用充值卡余额',
  `freeze_rc_balance` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '冻结充值卡余额',
  `inform_allow` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否允许举报(1可以/2不可以)',
  `is_buy` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '会员是否有购买权限 1为开启 0为关闭',
  `is_allowtalk` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '会员是否有咨询和发送站内信的权限 1为开启 0为关闭',
  `snsvisitnum` int(11) NOT NULL DEFAULT '0' COMMENT 'sns空间访问次数',
  `areaid` int(11) DEFAULT '0' COMMENT '地区ID',
  `cityid` int(11) DEFAULT '0' COMMENT '城市ID',
  `provinceid` int(11) DEFAULT '0' COMMENT '省份ID',
  `areainfo` varchar(255) DEFAULT '' COMMENT '地区内容',
  `exppoints` int(11) NOT NULL DEFAULT '0' COMMENT '会员经验值',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`name`),
  KEY `NewIndex2` (`email`),
  KEY `NewIndex3` (`mobile`),
  KEY `NewIndex4` (`qqopenid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='会员-会员';

/*Data for the table `imember_member` */

insert  into `imember_member`(`_id`,`email`,`email_bind`,`mobile`,`mobile_bind`,`state`,`nickname`,`avatar`,`passwd`,`paypwd`,`tel_mobile`,`sex`,`birthday`,`constellation`,`location`,`hometown`,`qq`,`monthly_income`,`signature`,`privacy`,`noticesettings`,`inviter_id`,`is_login_tip`,`is_smallmoney_open`,`smallmoney`,`register_by`,`ww`,`quicklink`,`buy_num`,`prized_num`,`login_num`,`reg_time`,`login_time`,`old_login_time`,`login_ip`,`old_login_ip`,`name`,`truename`,`qqopenid`,`qqinfo`,`sinaopenid`,`sinainfo`,`weixinopenid`,`weixininfo`,`points`,`available_predeposit`,`freeze_predeposit`,`available_rc_balance`,`freeze_rc_balance`,`inform_allow`,`is_buy`,`is_allowtalk`,`snsvisitnum`,`areaid`,`cityid`,`provinceid`,`areainfo`,`exppoints`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('569b72a3887c2206528b477c','1186904522@qq.com',1,'13564100096',1,1,'郭永荣','569e4337887c22834c8b460d_20150727112026494.jpg','44e14ba09ad041c42390b5b98d98e2a9','e10adc3949ba59abbe56e057f20f883e','55227050',1,'1979-12-11',9,'310000|310100|310110','310000|310200|310230','1186904522',4,'我是郭永荣','{\"msgSet\":2,\"areaSet\":1,\"searchSet\":1,\"buySet\":2,\"rafSet\":2,\"postSet\":2,\"buyShowNum\":10,\"rafShowNum\":0,\"postShowNum\":10}','{\"sysMsgSet\":1,\"wxMailSet\":1}','',1,1,50,1,'','',0,0,8,'2016-01-17 18:53:23','2016-01-23 15:12:47','2016-01-22 20:29:26','114.93.213.43','114.93.213.43','','','',NULL,'',NULL,'','',0,0,0,0,0,1,1,1,0,0,0,0,'',0,'{\"nickname_unique\":\"569e3ad3887c22024a8b46e1\",\"tel_mobile_unique\":\"569e3ad3887c22024a8b46e2\",\"sex_unique\":\"569e3ad3887c22024a8b46e3\",\"birthday_unique\":\"569e3ad3887c22024a8b46e4\",\"constellation_unique\":\"569e3ad3887c22024a8b46e5\",\"location_unique\":\"569e3ad3887c22024a8b46e6\",\"hometown_unique\":\"569e3ad3887c22024a8b46e7\",\"qq_unique\":\"569e3ad3887c22024a8b46e8\",\"monthly_income_unique\":\"569e3ad3887c22024a8b46e9\",\"signature_unique\":\"569e3ad3887c22024a8b46ea\"}','2016-01-17 18:53:23','2016-01-23 20:23:25',0),('569cdf08887c22774a8b57cd','handsomegyr@126.com',1,'',0,1,'','UserFace-160-0000.jpg','44e14ba09ad041c42390b5b98d98e2a9','','',0,'0000-00-00',0,'','','',0,'','{\"msgSet\":1,\"areaSet\":0,\"searchSet\":1,\"buySet\":0,\"rafSet\":0,\"postSet\":0,\"buyShowNum\":0,\"rafShowNum\":0,\"postShowNum\":0}','{\"sysMsgSet\":0,\"wxMailSet\":0}','569b72a3887c2206528b4782',0,0,0,2,'','',0,0,1,'2016-01-18 20:48:08','2016-01-18 20:48:08','2016-01-18 20:48:08','114.93.213.43','114.93.213.43','','','',NULL,'',NULL,'','',0,0,0,0,0,1,1,1,0,0,0,0,'',0,'','2016-01-18 20:48:08','2016-01-18 20:48:08',0),('56a06e62887c22cf598b45cf','',0,'15821039514',1,1,'','UserFace-160-0000.jpg','1cdb696e354460dd8259f49a6ecd2d7f','','',0,'0000-00-00',0,'','','',0,'','{\"msgSet\":1,\"areaSet\":0,\"searchSet\":0,\"buySet\":0,\"rafSet\":0,\"postSet\":0,\"buyShowNum\":0,\"rafShowNum\":0,\"postShowNum\":0}','{\"sysMsgSet\":0,\"wxMailSet\":0}','0',0,1,50,1,'','',0,0,7,'2016-01-21 13:36:33','2016-01-23 20:32:52','2016-01-23 17:13:53','180.154.12.216','180.154.12.216','','','123','{\"user_id\":\"123\",\"user_name\":\"Charlie\",\"user_headimgurl\":\"http:\\/\\/q.qlogo.cn\\/qqapp\\/100511748\\/63B5238639329AB202051A722BD115DB\\/100\",\"subscribe\":1}','',NULL,'123','{\"user_id\":\"123\",\"user_name\":\"Charlie\",\"user_headimgurl\":\"http:\\/\\/q.qlogo.cn\\/qqapp\\/100511748\\/63B5238639329AB202051A722BD115DB\\/100\",\"subscribe\":1}',0,0,0,0,0,1,1,1,0,0,0,0,'',0,'','2016-01-21 13:36:33','2016-01-23 20:32:52',0),('56a2f9fc887c224f7b8b4568','137103340@qq.com',1,'',0,1,'','UserFace-160-0000.jpg','ca91c0a4101789de878a100f8ed7da50','','',0,'0000-00-00',0,'','','',0,'','{\"msgSet\":1,\"areaSet\":0,\"searchSet\":0,\"buySet\":0,\"rafSet\":0,\"postSet\":0,\"buyShowNum\":0,\"rafShowNum\":0,\"postShowNum\":0}','{\"sysMsgSet\":0,\"wxMailSet\":0}','',0,0,0,2,'','',0,0,1,'2016-01-23 11:56:44','2016-01-23 11:56:44','2016-01-23 11:56:44','180.154.12.216','180.154.12.216','','','123','{\"user_id\":\"123\",\"user_name\":\"Charlie\",\"user_headimgurl\":\"http:\\/\\/q.qlogo.cn\\/qqapp\\/100511748\\/63B5238639329AB202051A722BD115DB\\/100\",\"subscribe\":1}','',NULL,'123','{\"user_id\":\"123\",\"user_name\":\"Charlie\",\"user_headimgurl\":\"http:\\/\\/q.qlogo.cn\\/qqapp\\/100511748\\/63B5238639329AB202051A722BD115DB\\/100\",\"subscribe\":1}',0,0,0,0,0,1,1,1,0,0,0,0,'',0,'','2016-01-23 11:56:44','2016-01-23 11:56:44',0);

/*Table structure for table `imember_news` */

DROP TABLE IF EXISTS `imember_news`;

CREATE TABLE `imember_news` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `user_id` char(24) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `user_avatar` varchar(100) NOT NULL DEFAULT '' COMMENT '用户头像',
  `user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户注册方式',
  `action` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '动态操作 1 购买 2 晒单',
  `content_id` char(24) NOT NULL DEFAULT '' COMMENT '操作对象ID',
  `memo` text NOT NULL COMMENT '备注',
  `news_time` datetime NOT NULL COMMENT '动态时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员-动态';

/*Data for the table `imember_news` */

/*Table structure for table `imember_report` */

DROP TABLE IF EXISTS `imember_report`;

CREATE TABLE `imember_report` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `from_user_id` char(24) NOT NULL DEFAULT '' COMMENT '举报用户ID',
  `from_user_nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '举报用户名称',
  `from_user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '举报用户邮箱',
  `from_user_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '举报用户手机',
  `from_user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '举报用户注册方式',
  `to_user_id` char(24) NOT NULL DEFAULT '' COMMENT '被举报用户ID',
  `to_user_nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '被举报用户名称',
  `to_user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '被举报用户邮箱',
  `to_user_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '被举报用户手机',
  `to_user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '被举报用户注册方式',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '举报类型 1钓鱼欺诈 2广告骚扰 3色情暴力 4其他',
  `content` text NOT NULL COMMENT '举报内容',
  `report_time` datetime NOT NULL COMMENT '举报时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`from_user_id`),
  KEY `NewIndex2` (`to_user_id`),
  KEY `NewIndex3` (`from_user_id`,`to_user_id`),
  KEY `NewIndex4` (`to_user_id`,`from_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员-举报';

/*Data for the table `imember_report` */

/*Table structure for table `imember_visitor` */

DROP TABLE IF EXISTS `imember_visitor`;

CREATE TABLE `imember_visitor` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `visited_user_id` char(24) NOT NULL DEFAULT '' COMMENT '被访问用户ID',
  `visit_user_id` char(24) NOT NULL DEFAULT '' COMMENT '访问用户ID',
  `browser_time` datetime NOT NULL COMMENT '浏览时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`visited_user_id`),
  KEY `NewIndex2` (`visit_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='会员-访客';

/*Data for the table `imember_visitor` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
