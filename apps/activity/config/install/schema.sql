/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.6.26 : Database - webcms
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

/*Table structure for table `iactivity_activity` */

DROP TABLE IF EXISTS `iactivity_activity`;

CREATE TABLE `iactivity_activity` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '活动分类',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '活动名称',
  `start_time` datetime NOT NULL COMMENT '活动开始时间',
  `end_time` datetime NOT NULL COMMENT '活动结束时间',
  `is_actived` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否激活',
  `is_paused` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否暂停',
  `config` text NOT NULL COMMENT '活动配置',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='活动-活动';

/*Data for the table `iactivity_activity` */

insert  into `iactivity_activity`(`_id`,`category`,`name`,`start_time`,`end_time`,`is_actived`,`is_paused`,`config`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5861e812887c22015f8b456b',3,'某抽奖活动','2016-12-27 00:00:00','2019-12-27 14:22:20',1,0,'','2016-12-27 12:03:30','2016-12-27 14:22:31',0);

/*Table structure for table `iactivity_user` */

DROP TABLE IF EXISTS `iactivity_user`;

CREATE TABLE `iactivity_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `worth` int(10) NOT NULL DEFAULT '0' COMMENT '价值',
  `worth2` int(11) NOT NULL DEFAULT '0' COMMENT '价值2',
  `redpack_user` char(50) NOT NULL DEFAULT '' COMMENT '微信红包账号',
  `thirdparty_user` char(50) NOT NULL DEFAULT '' COMMENT '第3方账号',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='活动-活动用戶';

/*Data for the table `iactivity_user` */

insert  into `iactivity_user`(`_id`,`activity_id`,`user_id`,`nickname`,`headimgurl`,`worth`,`worth2`,`redpack_user`,`thirdparty_user`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('58620e65887c22a17c8b4579','5861e812887c22015f8b456b','xxxx','xx','xx',0,0,'','','{\"prize_num\":0,\"is_hongbao1_lottery\":false,\"is_hongbao2_lottery\":false}','2016-12-27 14:47:00','2016-12-27 14:47:00',0),('58620f22887c2224678b4584','5861e812887c22015f8b456b','xxxx2','xx2','xx2',0,0,'redpack_user','thirdparty_user','{\"prize_num\":0,\"is_hongbao1_lottery\":false,\"is_hongbao2_lottery\":false}','2016-12-27 14:50:10','2016-12-27 14:50:10',0);

/*Table structure for table `iarticle_article` */

DROP TABLE IF EXISTS `iarticle_article`;

CREATE TABLE `iarticle_article` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `category_id` char(24) NOT NULL DEFAULT '' COMMENT '分类id',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '跳转链接',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示',
  `show_order` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `article_time` datetime NOT NULL COMMENT '发布时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章-文章';

/*Data for the table `iarticle_article` */

insert  into `iarticle_article`(`_id`,`category_id`,`url`,`is_show`,`show_order`,`title`,`content`,`article_time`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('568a6bc1887c2210688b461d','568a651d887c22014a8b4698','http://www.baidu.com/',1,1,'关于1元云购网遭到DDOS攻击的公告','亲爱的云友：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 大家好！2015年12月15日至16日，1元云购网遭到大规模的分布式拒绝服务（DDOS）攻击，攻击流量峰值达到每秒310G，造成网站无法打开，用户无法正常访问，此次攻击严重影响网站正常服务，对此我们深表歉意!&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 经1元云购网技术部门紧急防御，目前已成功阻挡了黑客的多次攻击，此次攻击属于DDOS大流量攻击，不会影响到网站的数据和各位云友的账号安全，请大家放心。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 黑客攻击行为严重威胁互联网安全，我们强烈谴责这次黑客攻击行动！目前1元云购网也已经对网站防护设备进行了调整升级，可以抵御更高级的攻击，我们将不遗余力保证网站正常运作。&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1元云购网&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2015-12-17','2016-01-04 20:54:38','2016-01-04 20:55:29','2016-01-04 21:47:57',0);

/*Table structure for table `iarticle_category` */

DROP TABLE IF EXISTS `iarticle_category`;

CREATE TABLE `iarticle_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '分类标识码',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `parent_id` char(24) NOT NULL DEFAULT '' COMMENT '父ID',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章-分类';

/*Data for the table `iarticle_category` */

insert  into `iarticle_category`(`_id`,`code`,`name`,`parent_id`,`sort`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('568a651d887c22014a8b4698','code1','分类1','',1,'2016-01-04 20:27:09','2016-01-04 20:27:09',0),('568a657a887c22184e8b460c','subcode1','子分类1','568a651d887c22014a8b4698',1,'2016-01-04 20:28:42','2016-01-04 20:28:42',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
