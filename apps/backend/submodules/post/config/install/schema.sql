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

/*Table structure for table `ipost_post` */

DROP TABLE IF EXISTS `ipost_post`;

CREATE TABLE `ipost_post` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '主题',
  `content` text NOT NULL COMMENT '内容',
  `pic` varchar(300) NOT NULL DEFAULT '' COMMENT '图片',
  `post_time` datetime NOT NULL COMMENT '帖子时间',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 -1未晒单 0待审核 1未通过 2审核通过',
  `fail_reason` text NOT NULL COMMENT '失败原因',
  `point` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '福分',
  `goods_commonid` char(24) NOT NULL DEFAULT '' COMMENT '商品公用ID',
  `goods_id` char(24) NOT NULL DEFAULT '' COMMENT '商品ID',
  `gc_id_1` char(24) NOT NULL DEFAULT '' COMMENT '商品分类第一层',
  `gc_id_2` char(24) NOT NULL DEFAULT '' COMMENT '商品分类第二层',
  `gc_id_3` char(24) NOT NULL DEFAULT '' COMMENT '商品分类第三层',
  `brand_id` char(24) NOT NULL DEFAULT '' COMMENT '品牌ID',
  `order_no` char(24) NOT NULL DEFAULT '' COMMENT '订单ID',
  `buyer_id` char(24) NOT NULL DEFAULT '' COMMENT '买家ID',
  `goods_info` text NOT NULL COMMENT '商品信息',
  `vote_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票数(羡慕数)',
  `reply_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `read_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '阅读数',
  `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐',
  `verify_time` datetime NOT NULL COMMENT '审核时间',
  `verify_user_id` char(24) NOT NULL DEFAULT '' COMMENT '审核人员ID',
  `verify_user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '审核人员姓名',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`order_no`),
  KEY `NewIndex2` (`buyer_id`),
  KEY `NewIndex3` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子-帖子';

/*Data for the table `ipost_post` */

/*Table structure for table `ipost_reply` */

DROP TABLE IF EXISTS `ipost_reply`;

CREATE TABLE `ipost_reply` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '回复ID',
  `post_id` char(24) NOT NULL DEFAULT '' COMMENT '帖子ID',
  `user_id` char(24) NOT NULL DEFAULT '' COMMENT '回复用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '回复用户姓名',
  `user_avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '回复用户头像',
  `user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '回复用户注册方式',
  `user_content` text NOT NULL COMMENT '回复内容',
  `to_user_id` char(24) NOT NULL DEFAULT '' COMMENT '话题用户ID',
  `to_user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '话题用户姓名',
  `to_user_avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '话题用户头像',
  `to_user_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '话题用户注册方式',
  `to_user_content` text NOT NULL COMMENT '话题内容',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复数量',
  `del_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复删除数量',
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复楼层id',
  `reply_time` datetime NOT NULL COMMENT '回复时间',
  `ref_reply_id` char(24) NOT NULL DEFAULT '' COMMENT '回复的回复ID',
  `ref_floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '回复楼层的楼层',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帖子-回复';

/*Data for the table `ipost_reply` */

/*Table structure for table `ipost_vote` */

DROP TABLE IF EXISTS `ipost_vote`;

CREATE TABLE `ipost_vote` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '投票ID',
  `post_id` char(24) NOT NULL DEFAULT '' COMMENT '帖子ID',
  `user_id` char(24) NOT NULL DEFAULT '' COMMENT '用户ID',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票数量',
  `vote_time` datetime NOT NULL COMMENT '投票时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`post_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='帖子-投票';

/*Data for the table `ipost_vote` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
