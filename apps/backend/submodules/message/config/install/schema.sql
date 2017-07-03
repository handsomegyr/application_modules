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

/*Table structure for table `imessage_msg` */

DROP TABLE IF EXISTS `imessage_msg`;

CREATE TABLE `imessage_msg` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '消息ID',
  `from_user_id` char(24) NOT NULL DEFAULT '' COMMENT '来自的用户ID',
  `to_user_id` char(24) NOT NULL DEFAULT '' COMMENT '发往的用户ID',
  `content` text NOT NULL COMMENT '消息内容',
  `msg_time` datetime NOT NULL COMMENT '消息时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`from_user_id`),
  KEY `NewIndex2` (`to_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息-消息';

/*Data for the table `imessage_msg` */

/*Table structure for table `imessage_msg_count` */

DROP TABLE IF EXISTS `imessage_msg_count`;

CREATE TABLE `imessage_msg_count` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `user_id` char(24) NOT NULL DEFAULT '' COMMENT '用户ID',
  `sysMsgCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '系统消息数量',
  `privMsgCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '私信数量',
  `friendMsgCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '好友申请消息数量',
  `replyMsgCount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复消息数量',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息-数量';

/*Data for the table `imessage_msg_count` */

insert  into `imessage_msg_count`(`_id`,`user_id`,`sysMsgCount`,`privMsgCount`,`friendMsgCount`,`replyMsgCount`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('569b72a3887c2206528b4783','569b72a3887c2206528b477c',0,0,0,0,'2016-01-17 18:53:23','2016-01-21 22:43:33',0),('569cdf08887c22774a8b57d4','569cdf08887c22774a8b57cd',0,0,0,0,'2016-01-18 20:48:08','2016-01-18 20:48:08',0),('56a06e62887c22cf598b45d6','56a06e62887c22cf598b45cf',0,0,0,0,'2016-01-21 13:36:33','2016-01-21 13:36:33',0),('56a2f9fc887c224f7b8b456f','56a2f9fc887c224f7b8b4568',0,0,0,0,'2016-01-23 11:56:44','2016-01-23 15:13:30',0);

/*Table structure for table `imessage_msg_statistics` */

DROP TABLE IF EXISTS `imessage_msg_statistics`;

CREATE TABLE `imessage_msg_statistics` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `user1_id` char(24) NOT NULL DEFAULT '' COMMENT '用户1ID',
  `user2_id` char(24) NOT NULL DEFAULT '' COMMENT '用户2ID',
  `msg_user_id` char(24) NOT NULL DEFAULT '' COMMENT '消息发送者',
  `msg_time` datetime NOT NULL COMMENT '消息时间',
  `content` text NOT NULL COMMENT '消息内容',
  `msg_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消息数量',
  `user1_unread_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户1未读数量',
  `user2_unread_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户2未读数量',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息-消息统计';

/*Data for the table `imessage_msg_statistics` */

/*Table structure for table `imessage_replymsg` */

DROP TABLE IF EXISTS `imessage_replymsg`;

CREATE TABLE `imessage_replymsg` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '回复消息ID',
  `relate_id` char(24) NOT NULL DEFAULT '' COMMENT '相关ID',
  `reply_user_id` char(24) NOT NULL DEFAULT '' COMMENT '回复用户ID',
  `reply_user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '回复用户姓名',
  `reply_user_avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '回复用户头像',
  `reply_user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '回复用户注册方式',
  `reply_content` text NOT NULL COMMENT '回复内容',
  `to_user_id` char(24) NOT NULL DEFAULT '' COMMENT '话题用户ID',
  `to_user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '话题用户姓名',
  `to_user_avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '话题用户头像',
  `to_user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '话题用户注册方式',
  `to_user_content` text NOT NULL COMMENT '话题内容',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读',
  `msg_time` datetime NOT NULL COMMENT '消息时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`reply_user_id`),
  KEY `NewIndex2` (`to_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息-回复';

/*Data for the table `imessage_replymsg` */

/*Table structure for table `imessage_sysmsg` */

DROP TABLE IF EXISTS `imessage_sysmsg`;

CREATE TABLE `imessage_sysmsg` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '消息ID',
  `to_user_id` char(24) NOT NULL DEFAULT '' COMMENT '发往的用户ID',
  `content` text NOT NULL COMMENT '消息内容',
  `msg_time` datetime NOT NULL COMMENT '消息时间',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`to_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息-系统消息';

/*Data for the table `imessage_sysmsg` */

/*Table structure for table `imessage_template` */

DROP TABLE IF EXISTS `imessage_template`;

CREATE TABLE `imessage_template` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '模板id',
  `code` varchar(30) NOT NULL DEFAULT '' COMMENT '模板调用代码',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '模板名称',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '模板标题',
  `content` text NOT NULL COMMENT '模板内容',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='消息-模板';

/*Data for the table `imessage_template` */

insert  into `imessage_template`(`_id`,`code`,`name`,`title`,`content`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('563dd559887c22d1498b4570','bind_email','[用户]邮箱验证通知','邮箱验证通知 - {$site_name}','<p>您好！</p><p>请在24小时内点击以下链接完成邮箱验证</p><p><a href=\"{$verify_url}\" target=\"_blank\">马上验证</a></p><p>如果您不能点击上面链接，还可以将以下链接复制到浏览器地址栏中访问</p><p>{$verify_url}</p>','2015-11-07 18:41:29','2015-11-07 19:09:17',0),('5645e9e7887c22f25e8b4569','validate_mobile','验证手机','验证手机','【云片网】您的验证码是{$vcode}','2015-11-13 21:47:19','2015-11-16 22:09:03',0),('566d6091887c22044a8b45a1','validate_email','验证邮箱','用户邮箱注册－验证码?','亲爱的{$userEmail}：\r\n您好！感谢您注册1元云购。\r\n\r\n您本次验证码为：{$vcode}，请及时输入。\r\n\r\n此邮件由系统自动发出，请勿回复！','2015-12-13 20:12:01','2015-12-13 20:28:29',0),('56a3106d887c22cf598b45d7','lottery_ok_email','1元云购揭晓邮件通知','1元云购揭晓通知','首页我的1元云购帮助亲爱的 中个奖有这么困难嘛：您好！恭喜您获得“(第74期)川宇（Kawau）Micro SD/T-Flash TF读卡器 C289”商品，请您及时登录“我的1元云购”-“获得的商品”填写收货地址。立即填写收货地址如果您有任何疑问，请与我们联系。客服热线：400-850-8080客服邮箱：service@1yyg.com此邮件由系统自动发出，请勿回复！(NO.742685)感谢您对1元云购（http://www.1yyg.com）的支持，祝您好运！客服热线：400-850-8080','2016-01-23 13:32:29','2016-01-23 14:58:12',0),('56a31be0887c2206528b4785','lottery_ok_mobile','1元云购揭晓短信通知','1元云购揭晓通知','【1元云购】恭喜您成为商品获得者，请您及时登录1元云购会员中心填写收货地址，客服电话：400-850-8080','2016-01-23 14:21:20','2016-01-23 14:21:20',0),('56a320ea887c22cf6c8b46d9','lottery_no','1元云购未获得商品通知','1元云购未获得商品通知','很遗憾，你未获得商品','2016-01-23 14:42:50','2016-01-23 14:42:50',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
