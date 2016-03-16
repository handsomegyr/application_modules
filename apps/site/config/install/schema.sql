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

/*Table structure for table `isite_banner` */

DROP TABLE IF EXISTS `isite_banner`;

CREATE TABLE `isite_banner` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `url` varchar(300) NOT NULL DEFAULT '' COMMENT '网址',
  `img` varchar(300) NOT NULL DEFAULT '' COMMENT '图片',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `show_order` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告图';

/*Table structure for table `isite_site` */

DROP TABLE IF EXISTS `isite_site`;

CREATE TABLE `isite_site` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '网站id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '网站名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '网站标题',
  `keywords` varchar(100) NOT NULL DEFAULT '' COMMENT '网站关键词',
  `description` text NOT NULL COMMENT '网站描述',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT '网站Logo',
  `mobile_logo` varchar(100) NOT NULL DEFAULT '' COMMENT '手机网站LOGO',
  `member_logo` varchar(100) NOT NULL DEFAULT '' COMMENT '会员中心Logo',
  `logowx` varchar(100) NOT NULL DEFAULT '' COMMENT '微信二维码',
  `icp_number` varchar(50) NOT NULL DEFAULT '' COMMENT 'ICP证书号',
  `phone` varchar(100) NOT NULL DEFAULT '' COMMENT '平台客服联系电话,以，分割',
  `tel400` varchar(50) NOT NULL DEFAULT '' COMMENT '前台客服电话',
  `email` varchar(30) NOT NULL DEFAULT '' COMMENT '电子邮件',
  `statistics_code` varchar(500) NOT NULL DEFAULT '' COMMENT '第三方流量统计代码',
  `time_zone` varchar(50) NOT NULL DEFAULT '' COMMENT '默认时区',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '站点状态',
  `closed_reason` text COMMENT '关闭原因',
  `image_dir_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '图片存放类型',
  `image_max_filesize` int(11) NOT NULL DEFAULT '1024' COMMENT '图片文件大小',
  `image_allow_ext` char(100) NOT NULL DEFAULT '' COMMENT '图片扩展名',
  `default_goods_image` varchar(100) NOT NULL DEFAULT '' COMMENT '默认商品图片',
  `default_user_portrait` varchar(100) NOT NULL DEFAULT '' COMMENT '默认会员头像',
  `hot_search` varchar(50) NOT NULL DEFAULT '' COMMENT '热门搜索',
  `md5_key` char(50) NOT NULL DEFAULT '' COMMENT 'Md5密钥',
  `guest_comment` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许游客咨询',
  `copyrights` text COMMENT '版权信息',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站表';

/*Table structure for table `isite_suggestion` */

DROP TABLE IF EXISTS `isite_suggestion`;

CREATE TABLE `isite_suggestion` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `theme` varchar(50) NOT NULL DEFAULT '' COMMENT '主题',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `telephone` varchar(20) NOT NULL DEFAULT '' COMMENT '电话',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT 'Email',
  `content` varchar(1000) NOT NULL DEFAULT '' COMMENT '反馈内容',
  `log_time` datetime NOT NULL COMMENT '记录时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站-建议';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
