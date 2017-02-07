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

/*Table structure for table `iweixin_application` */

DROP TABLE IF EXISTS `iweixin_application`;

CREATE TABLE `iweixin_application` (
  `_id` char(24) NOT NULL DEFAULT '',
  `weixin_name` varchar(30) NOT NULL DEFAULT '',
  `weixin_id` char(20) NOT NULL DEFAULT '',
  `verify_token` char(50) NOT NULL DEFAULT '',
  `appid` char(20) NOT NULL DEFAULT '',
  `secret` char(45) NOT NULL DEFAULT '',
  `access_token` char(110) DEFAULT '',
  `access_token_expire` datetime DEFAULT NULL,
  `is_advanced` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_product` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `secretKey` char(50) NOT NULL DEFAULT '',
  `jsapi_ticket` char(110) DEFAULT '',
  `jsapi_ticket_expire` datetime DEFAULT NULL,
  `is_weixin_card` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `wx_card_api_ticket` char(110) DEFAULT '',
  `wx_card_api_ticket_expire` datetime DEFAULT NULL,
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_application` */

/*Table structure for table `iweixin_callbackurls` */

DROP TABLE IF EXISTS `iweixin_callbackurls`;

CREATE TABLE `iweixin_callbackurls` (
  `_id` char(24) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `is_valid` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_callbackurls` */

insert  into `iweixin_callbackurls`(`_id`,`url`,`is_valid`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560df631fd4d3db2018b4567','.umaman.com',1,'2015-10-02 11:12:49','2015-10-02 11:12:49',0);

/*Table structure for table `iweixin_gender` */

DROP TABLE IF EXISTS `iweixin_gender`;

CREATE TABLE `iweixin_gender` (
  `_id` char(24) NOT NULL DEFAULT '',
  `key` varchar(10) NOT NULL DEFAULT '',
  `value` smallint(6) unsigned NOT NULL DEFAULT '0',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_gender` */

insert  into `iweixin_gender`(`_id`,`key`,`value`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560d29effd4d3d77018b4568','保密',0,'2015-10-01 20:41:19','2015-10-01 20:41:19',0),('560d29fefd4d3d30018b4569','男性',1,'2015-10-01 20:41:34','2015-10-01 20:41:34',0),('560d2a07fd4d3d30018b456a','女性',2,'2015-10-01 20:41:43','2015-10-01 20:41:43',0);

/*Table structure for table `iweixin_keyword` */

DROP TABLE IF EXISTS `iweixin_keyword`;

CREATE TABLE `iweixin_keyword` (
  `_id` char(24) NOT NULL DEFAULT '',
  `keyword` varchar(10) NOT NULL DEFAULT '',
  `fuzzy` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `reply_type` smallint(6) unsigned NOT NULL DEFAULT '0',
  `reply_ids` text NOT NULL,
  `priority` smallint(6) unsigned NOT NULL DEFAULT '0',
  `times` smallint(6) unsigned NOT NULL DEFAULT '0',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_keyword` */

insert  into `iweixin_keyword`(`_id`,`keyword`,`fuzzy`,`reply_type`,`reply_ids`,`priority`,`times`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('55daede57f50ea400a000011','默认回复',1,1,'[\"55e6c6d07f50ea6405000001\",\"55dae2767f50ea400a00000f\"]',1,5,'0000-00-00 00:00:00','2015-10-01 10:44:11',0);

/*Table structure for table `iweixin_menu` */

DROP TABLE IF EXISTS `iweixin_menu`;

CREATE TABLE `iweixin_menu` (
  `_id` char(24) NOT NULL DEFAULT '',
  `type` varchar(30) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  `key` varchar(30) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `parent` char(24) NOT NULL DEFAULT '',
  `priority` int(11) unsigned NOT NULL DEFAULT '0',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_menu` */

insert  into `iweixin_menu`(`_id`,`type`,`name`,`key`,`url`,`parent`,`priority`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560d3787fd4d3d32018b456b','click','远东互通','远东互通','','',1,'2015-10-01 21:39:19','2015-10-01 21:39:19',0),('560d37abfd4d3d7c018b4567','view','留言信箱','留言信箱','http://www.baidu.com/','560d3787fd4d3d32018b456b',1,'2015-10-01 21:39:55','2015-10-01 21:39:55',0),('560d37ccfd4d3d89018b4567','click','活动资讯','活动资讯','','560d3787fd4d3d32018b456b',2,'2015-10-01 21:40:28','2015-10-01 21:40:28',0),('560d3998fd4d3d7d018b4567','click','远东来客','远东来客','','',3,'2015-10-01 21:48:08','2015-10-01 21:48:08',0),('560d3a20fd4d3d87018b4567','view','联系我们','联系我们','http://www.baidu.com/','560d3998fd4d3d7d018b4567',1,'2015-10-01 21:50:24','2015-10-01 21:50:24',0),('560d3a46fd4d3d9b018b4567','click','远东见闻','远东见闻','','',2,'2015-10-01 21:51:02','2015-10-01 21:51:02',0),('560d3a61fd4d3d98018b4567','click','行业快报','行业快报','','560d3a46fd4d3d9b018b4567',1,'2015-10-01 21:51:29','2015-10-01 21:51:29',0);

/*Table structure for table `iweixin_menu_type` */

DROP TABLE IF EXISTS `iweixin_menu_type`;

CREATE TABLE `iweixin_menu_type` (
  `_id` char(24) NOT NULL DEFAULT '',
  `key` varchar(30) NOT NULL DEFAULT '',
  `value` varchar(30) NOT NULL DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_menu_type` */

insert  into `iweixin_menu_type`(`_id`,`key`,`value`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560d00a8fd4d3d2f018b4567','点击事件','click','2015-10-01 17:45:12','2015-10-01 17:45:12',0),('560d00b9fd4d3d32018b4567','链接跳转','view','2015-10-01 17:45:29','2015-10-01 17:45:29',0),('560d00e2fd4d3d30018b4567','扫码推事件','scancode_push','2015-10-01 17:46:10','2015-10-01 17:46:10',0),('560d00effd4d3d78018b4567','扫码推事件且弹出“消息接收中”提示框','scancode_waitmsg','2015-10-01 17:46:23','2015-10-01 17:46:23',0),('560d00fcfd4d3d77018b4567','弹出系统拍照发图','pic_sysphoto','2015-10-01 17:46:36','2015-10-01 17:46:36',0),('560d0108fd4d3d1d018b4568','弹出拍照或者相册发图','pic_photo_or_album','2015-10-01 17:46:48','2015-10-01 17:46:48',0),('560d0113fd4d3d24018b4567','弹出微信相册发图器','pic_weixin','2015-10-01 17:46:59','2015-10-01 17:46:59',0),('560d011ffd4d3d2f018b4568','弹出地理位置选择器','location_select','2015-10-01 17:47:11','2015-10-01 17:47:11',0);

/*Table structure for table `iweixin_msg_type` */

DROP TABLE IF EXISTS `iweixin_msg_type`;

CREATE TABLE `iweixin_msg_type` (
  `_id` char(24) NOT NULL DEFAULT '',
  `key` varchar(30) NOT NULL DEFAULT '',
  `value` varchar(30) NOT NULL DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_msg_type` */

insert  into `iweixin_msg_type`(`_id`,`key`,`value`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560d2a81fd4d3d2e018b4567','事件消息','event','2015-10-01 20:43:45','2015-10-01 20:43:45',0),('560d2a90fd4d3d2f018b4569','地理位置消息','location','2015-10-01 20:44:00','2015-10-01 20:44:00',0),('560d2a9dfd4d3d7a018b4568','图片消息','image','2015-10-01 20:44:13','2015-10-01 20:44:13',0),('560d2aa8fd4d3d77018b4569','文本消息','text','2015-10-01 20:44:24','2015-10-01 20:44:24',0),('560d2ab4fd4d3d30018b456b','链接消息','link','2015-10-01 20:44:36','2015-10-01 20:44:36',0),('560d2ac1fd4d3d32018b4569','视频消息','video','2015-10-01 20:44:49','2015-10-01 20:44:49',0),('560d2accfd4d3d33018b4567','语音消息','voice','2015-10-01 20:45:00','2015-10-01 20:45:00',0);

/*Table structure for table `iweixin_not_keyword` */

DROP TABLE IF EXISTS `iweixin_not_keyword`;

CREATE TABLE `iweixin_not_keyword` (
  `_id` char(24) NOT NULL DEFAULT '',
  `msg` varchar(30) NOT NULL DEFAULT '',
  `times` int(11) unsigned NOT NULL DEFAULT '0',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_not_keyword` */

insert  into `iweixin_not_keyword`(`_id`,`msg`,`times`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560df533fd4d3dab018b4567','vvvv',0,'2015-10-02 11:08:35','2015-10-02 11:08:35',0);

/*Table structure for table `iweixin_page` */

DROP TABLE IF EXISTS `iweixin_page`;

CREATE TABLE `iweixin_page` (
  `_id` char(24) NOT NULL DEFAULT '',
  `title` varchar(30) NOT NULL DEFAULT '',
  `picture` varchar(100) NOT NULL DEFAULT '',
  `content` text,
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_page` */

insert  into `iweixin_page`(`_id`,`title`,`picture`,`content`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560df51efd4d3db1018b4567','标题','图片','内容','2015-10-02 11:08:14','2015-10-02 11:08:14',0);

/*Table structure for table `iweixin_qrcode` */

DROP TABLE IF EXISTS `iweixin_qrcode`;

CREATE TABLE `iweixin_qrcode` (
  `_id` char(24) NOT NULL DEFAULT '',
  `scene_id` char(24) NOT NULL DEFAULT '',
  `openid` char(30) NOT NULL DEFAULT '',
  `Event` char(20) NOT NULL DEFAULT '',
  `EventKey` char(20) NOT NULL DEFAULT '',
  `Ticket` char(100) DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_qrcode` */

insert  into `iweixin_qrcode`(`_id`,`scene_id`,`openid`,`Event`,`EventKey`,`Ticket`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560df28bfd4d3db0018b4567','1','333','sdfsdf','sdfsdf','sdfsdf','2015-10-02 10:57:15','2015-10-02 10:57:15',0);

/*Table structure for table `iweixin_reply` */

DROP TABLE IF EXISTS `iweixin_reply`;

CREATE TABLE `iweixin_reply` (
  `_id` char(24) NOT NULL DEFAULT '',
  `reply_type` smallint(6) unsigned NOT NULL DEFAULT '0',
  `keyword` varchar(10) NOT NULL DEFAULT '',
  `title` varchar(30) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `picture` varchar(100) DEFAULT '',
  `icon` varchar(100) DEFAULT '',
  `music` varchar(100) DEFAULT '',
  `voice` varchar(100) DEFAULT '',
  `video` varchar(100) DEFAULT '',
  `image` varchar(100) DEFAULT '',
  `priority` smallint(6) unsigned NOT NULL DEFAULT '0',
  `page` varchar(100) NOT NULL DEFAULT '',
  `show_times` int(11) unsigned NOT NULL DEFAULT '0',
  `click_times` int(11) unsigned NOT NULL DEFAULT '0',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_reply` */

insert  into `iweixin_reply`(`_id`,`reply_type`,`keyword`,`title`,`url`,`description`,`picture`,`icon`,`music`,`voice`,`video`,`image`,`priority`,`page`,`show_times`,`click_times`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('55dae2767f50ea400a00000f',1,'默认回复','标题','网址链接','<p>亲，今天你参加[极限挑战]了么？极限红包等着你，快来吧！<img alt=\"6630190355419536199.png\" src=\"/ueditor/php/upload/image/20151012/1444640786134125.png\" title=\"1444640786134125.png\" /></p>','banner图片','小图标','音乐','音频','视频','图片',0,'自定义页面',0,0,'0000-00-00 00:00:00','2015-10-12 18:02:34',0),('55e6c6d07f50ea6405000001',3,'测试1','测试1','测试1','<p>测试1</p>','561cd8ae7f50ea081b000029','','','','','561cd8c57f50ea381b000029',12,'测试1',0,0,'2015-09-02 17:52:16','2015-11-07 19:05:20',0);

/*Table structure for table `iweixin_reply_type` */

DROP TABLE IF EXISTS `iweixin_reply_type`;

CREATE TABLE `iweixin_reply_type` (
  `_id` char(24) NOT NULL DEFAULT '',
  `key` varchar(10) NOT NULL DEFAULT '',
  `value` smallint(6) unsigned NOT NULL DEFAULT '0',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_reply_type` */

insert  into `iweixin_reply_type`(`_id`,`key`,`value`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('55dac7eb7f50ea400a00000e','图文',1,'0000-00-00 00:00:00','2015-10-01 14:11:33',0),('55dacb537f50eac00700000e','音乐',2,'0000-00-00 00:00:00','0000-00-00 00:00:00',0),('55dad5b07f50eac007000010','文本',3,'0000-00-00 00:00:00','0000-00-00 00:00:00',0),('55dad5c57f50eac007000011','音频',4,'0000-00-00 00:00:00','0000-00-00 00:00:00',0),('55dad5d37f50eac007000012','视频',5,'0000-00-00 00:00:00','0000-00-00 00:00:00',0),('55dad5dc7f50eac007000013','图片',6,'0000-00-00 00:00:00','2015-10-01 14:11:40',0);

/*Table structure for table `iweixin_scene` */

DROP TABLE IF EXISTS `iweixin_scene`;

CREATE TABLE `iweixin_scene` (
  `_id` char(24) NOT NULL DEFAULT '',
  `scene_id` char(10) NOT NULL DEFAULT '',
  `scene_name` varchar(30) NOT NULL DEFAULT '',
  `scene_desc` varchar(100) DEFAULT '',
  `subscribe_number` int(11) unsigned DEFAULT '0',
  `is_temporary` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `expire_seconds` int(11) unsigned NOT NULL DEFAULT '0',
  `ticket` char(100) NOT NULL DEFAULT '',
  `ticket_time` datetime DEFAULT NULL,
  `is_created` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_scene` */

insert  into `iweixin_scene`(`_id`,`scene_id`,`scene_name`,`scene_desc`,`subscribe_number`,`is_temporary`,`expire_seconds`,`ticket`,`ticket_time`,`is_created`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560df105fd4d3dad018b4567','1','场景1','场景描述',0,1,100,'',NULL,0,'2015-10-02 10:50:45','2015-10-02 10:50:45',0);

/*Table structure for table `iweixin_script_tracking` */

DROP TABLE IF EXISTS `iweixin_script_tracking`;

CREATE TABLE `iweixin_script_tracking` (
  `_id` char(24) NOT NULL DEFAULT '',
  `type` char(10) NOT NULL DEFAULT '',
  `start_time` int(11) unsigned NOT NULL DEFAULT '0',
  `end_time` int(11) unsigned NOT NULL DEFAULT '0',
  `execute_time` int(11) unsigned NOT NULL DEFAULT '0',
  `who` char(30) NOT NULL DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_script_tracking` */

insert  into `iweixin_script_tracking`(`_id`,`type`,`start_time`,`end_time`,`execute_time`,`who`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560df19afd4d3db3018b4567','xxx',10,20,30,'xxx','2015-10-02 10:53:14','2015-10-02 10:53:19',0);

/*Table structure for table `iweixin_sence` */

DROP TABLE IF EXISTS `iweixin_sence`;

CREATE TABLE `iweixin_sence` (
  `_id` char(24) NOT NULL DEFAULT '',
  `sence_id` char(10) NOT NULL DEFAULT '',
  `sence_name` varchar(30) NOT NULL DEFAULT '',
  `sence_desc` varchar(100) NOT NULL DEFAULT '',
  `subscribe_number` int(11) unsigned NOT NULL DEFAULT '0',
  `is_temporary` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `expire_seconds` int(11) unsigned NOT NULL DEFAULT '0',
  `ticket` char(100) NOT NULL DEFAULT '',
  `ticket_time` datetime NOT NULL,
  `is_created` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_sence` */

/*Table structure for table `iweixin_source` */

DROP TABLE IF EXISTS `iweixin_source`;

CREATE TABLE `iweixin_source` (
  `_id` char(24) NOT NULL DEFAULT '',
  `ToUserName` char(30) NOT NULL DEFAULT '',
  `FromUserName` char(30) NOT NULL DEFAULT '',
  `CreateTime` int(11) unsigned NOT NULL DEFAULT '0',
  `MsgType` char(10) NOT NULL DEFAULT '',
  `Content` varchar(200) DEFAULT '',
  `MsgId` char(20) DEFAULT '',
  `PicUrl` varchar(100) DEFAULT '',
  `MediaId` char(20) DEFAULT '',
  `Format` char(10) DEFAULT '',
  `ThumbMediaId` char(10) DEFAULT '',
  `Location_X` float DEFAULT '0',
  `Location_Y` float DEFAULT '0',
  `Scale` smallint(6) unsigned DEFAULT '0',
  `Label` varchar(100) DEFAULT '',
  `Title` varchar(100) DEFAULT '',
  `Description` text,
  `Url` varchar(100) DEFAULT '',
  `Event` char(10) DEFAULT '',
  `EventKey` char(50) DEFAULT '',
  `Ticket` char(100) DEFAULT '',
  `Latitude` float DEFAULT '0',
  `Longitude` float DEFAULT '0',
  `Precision` float DEFAULT '0',
  `interval` float DEFAULT '0',
  `coordinate` varchar(100) DEFAULT '',
  `Status` varchar(100) DEFAULT '',
  `request_xml` text NOT NULL,
  `response` text,
  `response_time` datetime DEFAULT NULL,
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_source` */

insert  into `iweixin_source`(`_id`,`ToUserName`,`FromUserName`,`CreateTime`,`MsgType`,`Content`,`MsgId`,`PicUrl`,`MediaId`,`Format`,`ThumbMediaId`,`Location_X`,`Location_Y`,`Scale`,`Label`,`Title`,`Description`,`Url`,`Event`,`EventKey`,`Ticket`,`Latitude`,`Longitude`,`Precision`,`interval`,`coordinate`,`Status`,`request_xml`,`response`,`response_time`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560deb75fd4d3da9018b4567','gh_6762364063ee','o6HiMuF61GElRIFGwfv8XkUzLO2Y',1443604783,'event','','','','','','',0,0,0,'','','','','VIEW','http://www.baidu.com/','',0,0,0,2.77119e+06,'','','1443604783','','2015-10-02 10:18:41','2015-10-02 10:27:01','2015-10-02 10:27:01',0);

/*Table structure for table `iweixin_subscribe_user` */

DROP TABLE IF EXISTS `iweixin_subscribe_user`;

CREATE TABLE `iweixin_subscribe_user` (
  `_id` char(24) NOT NULL DEFAULT '',
  `openid` char(30) NOT NULL DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_subscribe_user` */

insert  into `iweixin_subscribe_user`(`_id`,`openid`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560df675fd4d3da4018b4568','vvvv','2015-10-02 11:13:57','2015-10-02 11:13:57',0);

/*Table structure for table `iweixin_user` */

DROP TABLE IF EXISTS `iweixin_user`;

CREATE TABLE `iweixin_user` (
  `_id` char(24) NOT NULL DEFAULT '',
  `openid` char(30) NOT NULL DEFAULT '',
  `nickname` varchar(30) DEFAULT '',
  `sex` tinyint(1) unsigned DEFAULT '0',
  `country` varchar(30) DEFAULT '',
  `province` varchar(30) DEFAULT '',
  `city` varchar(30) DEFAULT '',
  `headimgurl` varchar(150) DEFAULT '',
  `privilege` text,
  `subscribe_time` datetime DEFAULT NULL,
  `subscribe` tinyint(1) unsigned DEFAULT '0',
  `access_token` text,
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `iweixin_user` */

insert  into `iweixin_user`(`_id`,`openid`,`nickname`,`sex`,`country`,`province`,`city`,`headimgurl`,`privilege`,`subscribe_time`,`subscribe`,`access_token`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('560ddba9fd4d3d9d018b4567','o6HiMuF61GElRIFGwfv8XkUzLO2Y','邬邬Verlaine',1,'中国','上海','黄浦','http://wx.qlogo.cn/mmopen/Q3auHgzwzM7UHa5pVr9e6B8rO6N6MoLnPmIupFsO1kofb9SN4UesJu4L5NrXaeAlkPyrSHKbgbPfkoY8zjazeQ/0','','2015-10-02 09:30:40',1,'','2015-10-02 09:19:37','2015-10-02 09:37:00',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
