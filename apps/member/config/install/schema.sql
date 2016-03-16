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
  `available_predeposit` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款可用金额',
  `freeze_predeposit` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款冻结金额',
  `available_rc_balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '可用充值卡余额',
  `freeze_rc_balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '冻结充值卡余额',
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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
