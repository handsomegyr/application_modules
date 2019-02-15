/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.6.38-log : Database - webcms
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

/*Table structure for table `ilive_auchor` */

DROP TABLE IF EXISTS `ilive_auchor`;

CREATE TABLE `ilive_auchor` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '主播名称',
  `openid` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `worth` int(10) NOT NULL DEFAULT '0' COMMENT '价值',
  `worth2` int(11) NOT NULL DEFAULT '0' COMMENT '价值2',
  `redpack_user` char(50) NOT NULL DEFAULT '' COMMENT '微信红包账号',
  `thirdparty_user` char(50) NOT NULL DEFAULT '' COMMENT '第3方账号',
  `contact_name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系信息.姓名',
  `contact_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系信息.手机',
  `contact_address` varchar(200) NOT NULL DEFAULT '' COMMENT '联系信息.地址',
  `is_vip` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是VIP用户',
  `is_test` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是测试主播',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`openid`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='直播-主播';

/*Data for the table `ilive_auchor` */

insert  into `ilive_auchor`(`_id`,`name`,`openid`,`nickname`,`headimgurl`,`worth`,`worth2`,`redpack_user`,`thirdparty_user`,`contact_name`,`contact_mobile`,`contact_address`,`is_vip`,`is_test`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5a681dbbcb6ef4077c710031','测试主播1','xxxx','测试主播1','http://qzapp.qlogo.cn/qzapp/221403/12EBD57369718EBF0CC9FC352C2969AB/100',0,0,'','','xxx','xx','xxx',0,0,'','2018-01-24 13:46:35','2018-01-24 13:46:35',0);

/*Table structure for table `ilive_resource` */

DROP TABLE IF EXISTS `ilive_resource`;

CREATE TABLE `ilive_resource` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `content` varchar(300) NOT NULL DEFAULT '' COMMENT '资源内容',
  `contentType` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '资源类型',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='直播-资源';

/*Data for the table `ilive_resource` */

insert  into `ilive_resource`(`_id`,`content`,`contentType`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5a6ee648cb6ef412f17853e1','昵称1',1,'2018-01-29 17:15:52','2018-01-29 17:15:52',0),('5a6ee654cb6ef4554c6f2aa1','昵称2',1,'2018-01-29 17:16:04','2018-01-29 17:16:04',0),('5a6ee65acb6ef454cb055791','昵称2',1,'2018-01-29 17:16:10','2018-01-29 17:16:10',0),('5a6ee65fcb6ef454c8184791','昵称3',1,'2018-01-29 17:16:15','2018-01-29 17:16:15',0),('5a6ee66bcb6ef410d4258c02','昵称4',1,'2018-01-29 17:16:27','2018-01-29 17:16:27',0),('5a6ee677cb6ef4641e79e3a1','昵称5',1,'2018-01-29 17:16:39','2018-01-29 17:16:39',0),('5a6ee684cb6ef410d347f251','昵称6',1,'2018-01-29 17:16:52','2018-01-29 17:16:52',0),('5a6ee691cb6ef46d7e284201','昵称8',1,'2018-01-29 17:17:05','2018-01-29 17:17:05',0),('5a6ee69ecb6ef454ca243531','昵称9',1,'2018-01-29 17:17:18','2018-01-29 17:17:18',0),('5a6ee6ffcb6ef454cb055792','http://cloud.umaman.com/file/59425550cff348847a8b551a',2,'2018-01-29 17:18:55','2018-01-29 17:18:55',0),('5a6ee713cb6ef454c8184792','http://cloud.umaman.com/file/59425550cff348847a8b5517',2,'2018-01-29 17:19:15','2018-01-29 17:19:15',0),('5a6ee722cb6ef410d4258c03','http://cloud.umaman.com/file/59425550cff348847a8b5514',2,'2018-01-29 17:19:30','2018-01-29 17:19:30',0),('5a6ee733cb6ef4641e79e3a2','http://cloud.umaman.com/file/59425550cff348847a8b5511',2,'2018-01-29 17:19:47','2018-01-29 17:19:47',0),('5a6ee741cb6ef410d347f252','http://cloud.umaman.com/file/59425550cff348847a8b550e',2,'2018-01-29 17:20:01','2018-01-29 17:20:01',0),('5a6ee77ccb6ef46d7e284202','http://cloud.umaman.com/file/59425550cff348847a8b550b',2,'2018-01-29 17:21:00','2018-01-29 17:21:00',0),('5a6ee78ecb6ef454ca243532','http://cloud.umaman.com/file/59425550cff348847a8b5508',2,'2018-01-29 17:21:18','2018-01-29 17:21:18',0),('5a6ee7a2cb6ef454cb055793','http://cloud.umaman.com/file/59425550cff348847a8b5505',2,'2018-01-29 17:21:38','2018-01-29 17:21:38',0),('5a6ee7afcb6ef454c8184793','http://cloud.umaman.com/file/59425550cff348847a8b5502',2,'2018-01-29 17:21:51','2018-01-29 17:21:51',0),('5a6ee7edcb6ef410d4258c04','哈哈哈，我来啦！',3,'2018-01-29 17:22:53','2018-01-29 17:22:53',0),('5a6ee7facb6ef4641e79e3a3','天天被种草~',3,'2018-01-29 17:23:06','2018-01-29 17:23:06',0),('5a6ee807cb6ef410d347f253','有意思的直播',3,'2018-01-29 17:23:19','2018-01-29 17:23:19',0),('5a6ee822cb6ef46d7e284203','实力担当！颜值担当！霸道总裁即视感！',3,'2018-01-29 17:23:46','2018-01-29 17:23:46',0),('5a6ee82ecb6ef454ca243533','哇咔咔~',3,'2018-01-29 17:23:58','2018-01-29 17:23:58',0),('5a6ee839cb6ef454cb055794','棒棒哒',3,'2018-01-29 17:24:09','2018-01-29 17:24:09',0),('5a6ee847cb6ef454c8184794','我看到了美女~',3,'2018-01-29 17:24:23','2018-01-29 17:24:23',0),('5a6ee862cb6ef410d4258c05','支持！',3,'2018-01-29 17:24:50','2018-01-29 17:24:50',0),('5a6ee86ecb6ef4641e79e3a4','红包捏？',3,'2018-01-29 17:25:02','2018-01-29 17:25:02',0),('5a6ee88acb6ef410d347f254','第一次来么？',4,'2018-01-29 17:25:30','2018-01-29 17:25:30',0),('5a6ee89ccb6ef46d7e284204','欢迎新来的朋友。',4,'2018-01-29 17:25:48','2018-01-29 17:25:48',0),('5a6ee8b8cb6ef454ca243534','又有新朋友进来。',4,'2018-01-29 17:26:16','2018-01-29 17:26:16',0),('5a6ee8c8cb6ef454cb055795','欢迎欢迎，热烈欢迎！',4,'2018-01-29 17:26:32','2018-01-29 17:26:32',0),('5a6ee907cb6ef454c8184795','新朋友越来越多了。',4,'2018-01-29 17:27:35','2018-01-29 17:27:35',0),('5a6ee913cb6ef410d4258c06','欢迎新人来直播间。',4,'2018-01-29 17:27:47','2018-01-29 17:27:47',0);

/*Table structure for table `ilive_room` */

DROP TABLE IF EXISTS `ilive_room`;

CREATE TABLE `ilive_room` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `auchor_id` char(24) NOT NULL DEFAULT '' COMMENT '主播ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '房间名称',
  `start_time` datetime NOT NULL COMMENT '房间开启时间',
  `end_time` datetime NOT NULL COMMENT '房间关闭时间',
  `is_opened` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '房间是否开启',
  `headline` varchar(200) NOT NULL DEFAULT '' COMMENT '房间简介',
  `bg_pic` varchar(300) NOT NULL DEFAULT '' COMMENT '房间背景图',
  `cover_pic` varchar(300) NOT NULL DEFAULT '' COMMENT '房间封面图',
  `is_test` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是测试房间',
  `show_order` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `is_direct` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否直接进入房间',
  `live_start_time` datetime NOT NULL COMMENT '直播开启时间',
  `live_end_time` datetime NOT NULL COMMENT '直播关闭时间',
  `live_push_url` varchar(300) NOT NULL DEFAULT '' COMMENT '直播推流地址',
  `live_play_url` varchar(300) NOT NULL DEFAULT '' COMMENT '直播播放地址',
  `live_replay_url` varchar(300) NOT NULL DEFAULT '' COMMENT '直播重播地址',
  `live_paused_bg_pic` varchar(300) NOT NULL DEFAULT '' COMMENT '直播暂停背景图',
  `live_closed_bg_pic` varchar(300) NOT NULL DEFAULT '' COMMENT '直播结束背景图',
  `live_closed_redirect_url` varchar(300) NOT NULL DEFAULT '' COMMENT '直播结束跳转地址',
  `live_is_closed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '直播是否结束',
  `live_is_paused` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '直播是否暂停',
  `live_is_replay` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '直播是否回放',
  `share_settings` text NOT NULL COMMENT '分享配置',
  `robot_settings` text NOT NULL COMMENT '机器人配置',
  `item_settings` text NOT NULL COMMENT '房间项目配置',
  `behavior_settings` text NOT NULL COMMENT '交互行为配置',
  `plugin_settings` text NOT NULL COMMENT '插件配置',
  `view_settings` text NOT NULL COMMENT '授权观看配置',
  `task_settings` text NOT NULL COMMENT '任务配置',
  `emoji_settings` text NOT NULL COMMENT '表情包配置',
  `category_settings` text NOT NULL COMMENT '栏目配置',
  `coupon_settings` text NOT NULL COMMENT '优惠券配置',
  `banner_settings` text NOT NULL COMMENT '横幅广告设置',
  `tag_settings` text NOT NULL COMMENT '标签设置',
  `view_max_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '房间最大围观人数',
  `view_random_num` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '围观人数随机数',
  `view_base_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '围观人数基数',
  `view_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '真实围观人数',
  `view_num_virtual` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟围观人数',
  `view_peak_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '围观峰值',
  `like_random_num` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '点赞随机数',
  `like_base_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点赞基数',
  `like_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '真实点赞人数',
  `like_num_virtual` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟点赞人数',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='直播-房间';

/*Data for the table `ilive_room` */

insert  into `ilive_room`(`_id`,`auchor_id`,`name`,`start_time`,`end_time`,`is_opened`,`headline`,`bg_pic`,`cover_pic`,`is_test`,`show_order`,`is_direct`,`live_start_time`,`live_end_time`,`live_push_url`,`live_play_url`,`live_replay_url`,`live_paused_bg_pic`,`live_closed_bg_pic`,`live_closed_redirect_url`,`live_is_closed`,`live_is_paused`,`live_is_replay`,`share_settings`,`robot_settings`,`item_settings`,`behavior_settings`,`plugin_settings`,`view_settings`,`task_settings`,`emoji_settings`,`category_settings`,`coupon_settings`,`banner_settings`,`tag_settings`,`view_max_num`,`view_random_num`,`view_base_num`,`view_num`,`view_num_virtual`,`view_peak_num`,`like_random_num`,`like_base_num`,`like_num`,`like_num_virtual`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5a4f2686cb6ef462f64ee4a1','5a681dbbcb6ef4077c710031','测试房间1','2018-01-05 14:40:02','2019-01-05 14:40:02',1,'测试房间1','来伊份直播间-常态背景图-观看_meitu_4.jpg','来伊份直播间-常态背景图-观看_meitu_4.jpg',0,1,1,'2018-01-05 14:40:02','2018-01-05 14:40:02','http://www.baidu.com/','http://www.baidu.com/','http://www.baidu.com/','来伊份直播间-常态背景图-暂离_meitu_6.jpg','来伊份直播间-常态背景图-结束_meitu_5.jpg','http://www.baidu.com/',0,0,1,'','{\"is_open\":1,\"is_use_default\":1,\"rate\":10,\"operation\":{\"chat\":1,\"barrage\":1,\"login\":1}}','','','','','','','','','','',10000,1,0,0,0,0,1,0,0,0,'','2018-01-05 15:17:26','2018-01-05 15:17:26',0),('5a4f26d9cb6ef462f7501ae1','5a681dbbcb6ef4077c710031','测试房间','2018-01-05 15:17:50','2019-01-05 15:17:50',1,'测试房间','来伊份直播间-常态背景图-观看_meitu_4.jpg','来伊份直播间-常态背景图-观看_meitu_4.jpg',0,1,0,'2018-01-05 15:17:50','2018-01-05 15:17:50','http://www.baidu.com/','http://www.baidu.com/','http://www.baidu.com/','来伊份直播间-常态背景图-暂离_meitu_6.jpg','来伊份直播间-常态背景图-结束_meitu_5.jpg','http://www.baidu.com/',0,0,1,'','{\"is_open\":1,\"is_use_default\":1,\"rate\":10,\"operation\":{\"chat\":1,\"barrage\":1,\"login\":1}}','','','','','','','','','','',10000,1,0,0,0,0,1,0,0,0,'','2018-01-05 15:18:49','2018-01-05 15:18:49',0);

/*Table structure for table `ilive_user` */

DROP TABLE IF EXISTS `ilive_user`;

CREATE TABLE `ilive_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `room_id` char(24) NOT NULL DEFAULT '' COMMENT '直播房间ID',
  `openid` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `worth` int(10) NOT NULL DEFAULT '0' COMMENT '价值',
  `worth2` int(11) NOT NULL DEFAULT '0' COMMENT '价值2',
  `redpack_user` char(50) NOT NULL DEFAULT '' COMMENT '微信红包账号',
  `thirdparty_user` char(50) NOT NULL DEFAULT '' COMMENT '第3方账号',
  `contact_name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系信息.姓名',
  `contact_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系信息.手机',
  `contact_address` varchar(200) NOT NULL DEFAULT '' COMMENT '联系信息.地址',
  `is_auchor` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是主播',
  `auchor_id` char(24) NOT NULL DEFAULT '' COMMENT '主播ID',
  `is_vip` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是VIP用户',
  `is_test` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是测试用户',
  `authtype` varchar(20) NOT NULL DEFAULT '' COMMENT '登陆方式',
  `source` varchar(20) NOT NULL DEFAULT '' COMMENT '来源',
  `channel` varchar(20) NOT NULL DEFAULT '' COMMENT '渠道',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`openid`,`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='直播-用戶';

/*Data for the table `ilive_user` */

insert  into `ilive_user`(`_id`,`room_id`,`openid`,`nickname`,`headimgurl`,`worth`,`worth2`,`redpack_user`,`thirdparty_user`,`contact_name`,`contact_mobile`,`contact_address`,`is_auchor`,`auchor_id`,`is_vip`,`is_test`,`authtype`,`source`,`channel`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('5a61bb3fcb6ef40749746631','5a4f2686cb6ef462f64ee4a1','xxx','郭永荣','http://qzapp.qlogo.cn/qzapp/221403/12EBD57369718EBF0CC9FC352C2969AB/100',0,0,'','','','','',1,'5a681dbbcb6ef4077c710031',0,0,'weixin','weixin','weixin','{\"first_room_id\":\"5a4f2686cb6ef462f64ee4a1\"}','2018-01-19 17:32:46','2018-02-02 16:36:43',0),('5a69b028cb6ef41adf21b7a1','5a4f2686cb6ef462f64ee4a1','xxx1','施丹','https://tfs.alipayobjects.com/images/partner/T1VpheXnCdXXXXXXXX',0,0,'','uxxx1','','','',0,'',0,0,'weixin','weixin','weixin','{\"first_room_id\":\"5a4f2686cb6ef462f64ee4a1\"}','2018-01-25 18:23:36','2018-02-02 16:36:53',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
