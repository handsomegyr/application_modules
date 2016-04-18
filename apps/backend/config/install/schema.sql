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

/*Table structure for table `activity` */

DROP TABLE IF EXISTS `activity`;

CREATE TABLE `activity` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '活动名称',
  `start_time` datetime NOT NULL COMMENT '活动开始时间',
  `end_time` datetime NOT NULL COMMENT '活动结束时间',
  `is_actived` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否激活',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动';

/*Table structure for table `area` */

DROP TABLE IF EXISTS `area`;

CREATE TABLE `area` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '地区ID',
  `code` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '地区编码',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '地区名称',
  `level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '级别',
  `parent_code` int(11) NOT NULL DEFAULT '0' COMMENT '上级地区编号',
  `parent_name` varchar(50) DEFAULT '' COMMENT '上级地区名称',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex2` (`parent_code`),
  KEY `NewIndex1` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='全国行政区划';

/*Table structure for table `enum` */

DROP TABLE IF EXISTS `enum`;

CREATE TABLE `enum` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '枚举ID',
  `code` tinyint(4) NOT NULL DEFAULT '0' COMMENT '枚举值',
  `name` char(50) NOT NULL DEFAULT '' COMMENT '枚举名',
  `pid` char(24) NOT NULL DEFAULT '' COMMENT '上级枚举',
  `show_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='枚举表';

/*Table structure for table `errorlog` */

DROP TABLE IF EXISTS `errorlog`;

CREATE TABLE `errorlog` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '错误记录ID',
  `error_code` int(11) NOT NULL DEFAULT '0' COMMENT '错误码',
  `error_message` varchar(1000) NOT NULL DEFAULT '' COMMENT '错误信息',
  `happen_time` datetime NOT NULL COMMENT '发生时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='错误日志';

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

/*Table structure for table `iexchange_limit` */

DROP TABLE IF EXISTS `iexchange_limit`;

CREATE TABLE `iexchange_limit` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换限制ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换限制奖品',
  `limit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '兑换限制数量',
  `start_time` datetime NOT NULL COMMENT '限制开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '限制结束时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='兑换-限制';

/*Table structure for table `iexchange_log` */

DROP TABLE IF EXISTS `iexchange_log`;

CREATE TABLE `iexchange_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换日志ID',
  `result_code` smallint(6) NOT NULL DEFAULT '0' COMMENT '兑换结果',
  `result_msg` text NOT NULL COMMENT '兑换结果说明',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `rule_id` char(24) NOT NULL DEFAULT '' COMMENT '规则ID',
  `quantity` int(11) NOT NULL DEFAULT '0' COMMENT '兑换数量',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '兑换积分',
  `success_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换成功ID',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='兑换-日志';

/*Table structure for table `iexchange_rule` */

DROP TABLE IF EXISTS `iexchange_rule`;

CREATE TABLE `iexchange_rule` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换规则ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换奖品',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '可兑换数量',
  `start_time` datetime NOT NULL COMMENT '兑换开始时间',
  `end_time` datetime NOT NULL COMMENT '兑换结束时间',
  `score_category` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '积分分类',
  `score` int(10) NOT NULL COMMENT '兑换所需积分',
  `exchange_quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '已兑换数量',
  `sort` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序[从小到大]',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='兑换-规则';

/*Table structure for table `iexchange_success` */

DROP TABLE IF EXISTS `iexchange_success`;

CREATE TABLE `iexchange_success` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '兑换成功ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户编号',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `quantity` int(10) NOT NULL DEFAULT '0' COMMENT '数量',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '兑换积分',
  `is_valid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效',
  `exchange_time` datetime NOT NULL COMMENT '兑换时间',
  `rule_id` char(24) NOT NULL DEFAULT '' COMMENT '兑奖规则ID',
  `prize_code` char(24) NOT NULL DEFAULT '' COMMENT '奖品信息.奖品代码',
  `prize_name` varchar(50) NOT NULL DEFAULT '' COMMENT '奖品信息.奖品名字',
  `prize_category` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '奖品信息.奖品类别',
  `prize_is_virtual` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '奖品信息.是否实物奖',
  `prize_virtual_currency` int(11) NOT NULL DEFAULT '0' COMMENT '奖品信息.奖品价值',
  `prize_virtual_code` char(24) NOT NULL DEFAULT '' COMMENT '券码信息.卡号',
  `prize_virtual_pwd` char(30) NOT NULL DEFAULT '' COMMENT '券码信息.密码',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户信息.姓名',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户信息.头像',
  `contact_name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系信息.姓名',
  `contact_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系信息.手机号',
  `contact_address` varchar(300) NOT NULL DEFAULT '' COMMENT '联系信息.地址',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`prize_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='兑换-成功记录';

/*Table structure for table `ifreight_express` */

DROP TABLE IF EXISTS `ifreight_express`;

CREATE TABLE `ifreight_express` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '公司名称',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '编号',
  `letter` char(1) NOT NULL DEFAULT '' COMMENT '首字母',
  `is_order` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否常用',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '公司网址',
  `zt_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否支持服务站配送0否1是',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='运价-快递公司';

/*Table structure for table `igoods_ad` */

DROP TABLE IF EXISTS `igoods_ad`;

CREATE TABLE `igoods_ad` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `goods_id` char(24) NOT NULL DEFAULT '' COMMENT '商品ID',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示',
  `show_order` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品-广告位';

/*Table structure for table `igoods_attr_index` */

DROP TABLE IF EXISTS `igoods_attr_index`;

CREATE TABLE `igoods_attr_index` (
  `_id` char(24) NOT NULL DEFAULT '',
  `goods_id` char(24) NOT NULL DEFAULT '' COMMENT '商品id',
  `goods_commonid` char(24) NOT NULL DEFAULT '' COMMENT '商品公共表id',
  `gc_id` char(24) NOT NULL DEFAULT '' COMMENT '商品分类id',
  `type_id` char(24) DEFAULT '' COMMENT '类型id',
  `attr_id` char(24) DEFAULT '' COMMENT '属性id',
  `attr_value_id` char(24) DEFAULT '' COMMENT '属性值id',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_goods_id` int(11) DEFAULT NULL,
  `shopnc_goods_commonid` int(11) DEFAULT NULL,
  `shopnc_gc_id` int(11) DEFAULT NULL,
  `shopnc_type_id` int(11) DEFAULT NULL,
  `shopnc_attr_id` int(11) DEFAULT NULL,
  `shopnc_attr_value_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`),
  UNIQUE KEY `key1` (`goods_id`,`gc_id`,`attr_value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品与属性对应表';

/*Table structure for table `igoods_attribute` */

DROP TABLE IF EXISTS `igoods_attribute`;

CREATE TABLE `igoods_attribute` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '属性id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '属性名称',
  `type_id` char(24) NOT NULL DEFAULT '' COMMENT '所属类型id',
  `attr_value` text NOT NULL COMMENT '属性值列',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示。0为不显示、1为显示',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_attr_id` int(11) DEFAULT NULL,
  `shopnc_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品属性表';

/*Table structure for table `igoods_attribute_value` */

DROP TABLE IF EXISTS `igoods_attribute_value`;

CREATE TABLE `igoods_attribute_value` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '属性值id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '属性值名称',
  `attr_id` char(24) NOT NULL DEFAULT '' COMMENT '所属属性id',
  `type_id` char(24) NOT NULL DEFAULT '' COMMENT '类型id',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '属性值排序',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_attr_value_id` int(11) DEFAULT NULL,
  `shopnc_attr_id` int(11) DEFAULT NULL,
  `shopnc_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品属性值表';

/*Table structure for table `igoods_brand` */

DROP TABLE IF EXISTS `igoods_brand`;

CREATE TABLE `igoods_brand` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '品牌ID',
  `name` varchar(100) DEFAULT '' COMMENT '品牌名称',
  `initial` varchar(1) NOT NULL DEFAULT '' COMMENT '品牌首字母',
  `category_name` varchar(50) DEFAULT '' COMMENT '类别名称',
  `pic` varchar(100) DEFAULT '' COMMENT '图片',
  `sort` tinyint(3) unsigned DEFAULT '0' COMMENT '排序',
  `recommend` tinyint(1) unsigned DEFAULT '0' COMMENT '推荐，0为否，1为是，默认为0',
  `store_id` char(24) NOT NULL DEFAULT '' COMMENT '店铺ID',
  `apply` tinyint(1) NOT NULL DEFAULT '1' COMMENT '品牌申请，0为申请中，1为通过，默认为1，申请功能是会员使用，系统后台默认为1',
  `category_id` char(24) DEFAULT '' COMMENT '所属分类id',
  `show_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '品牌展示类型 0表示图片 1表示文字 ',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_brand_id` int(11) DEFAULT NULL,
  `shopnc_class_id` int(11) DEFAULT NULL,
  `shopnc_store_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='品牌表';

/*Table structure for table `igoods_browse` */

DROP TABLE IF EXISTS `igoods_browse`;

CREATE TABLE `igoods_browse` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '浏览商品ID',
  `goods_id` char(24) NOT NULL DEFAULT '' COMMENT '商品ID',
  `member_id` char(24) NOT NULL DEFAULT '' COMMENT '会员ID',
  `browse_time` datetime NOT NULL COMMENT '浏览时间',
  `gc_id` char(24) NOT NULL DEFAULT '' COMMENT '商品分类',
  `gc_id_1` char(24) NOT NULL DEFAULT '' COMMENT '商品一级分类',
  `gc_id_2` char(24) NOT NULL DEFAULT '' COMMENT '商品二级分类',
  `gc_id_3` char(24) NOT NULL DEFAULT '' COMMENT '商品三级分类',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`),
  UNIQUE KEY `key1` (`goods_id`,`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品浏览历史表';

/*Table structure for table `igoods_category` */

DROP TABLE IF EXISTS `igoods_category`;

CREATE TABLE `igoods_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '分类ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '分类名称',
  `type_id` char(24) NOT NULL DEFAULT '' COMMENT '类型id',
  `type_name` varchar(100) NOT NULL DEFAULT '' COMMENT '类型名称',
  `parent_id` char(24) DEFAULT '' COMMENT '父ID',
  `commis_rate` float(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '佣金比例',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `virtual` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许发布虚拟商品，1是，0否',
  `title` varchar(200) DEFAULT '' COMMENT '名称',
  `keywords` varchar(255) DEFAULT '' COMMENT '关键词',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_class_id` int(11) DEFAULT NULL,
  `shopnc_class_parent_id` int(11) DEFAULT NULL,
  `shopnc_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品分类表';

/*Table structure for table `igoods_category_tag` */

DROP TABLE IF EXISTS `igoods_category_tag`;

CREATE TABLE `igoods_category_tag` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'TAGid',
  `gc_id_1` char(24) NOT NULL DEFAULT '' COMMENT '一级分类id',
  `gc_id_2` char(24) NOT NULL DEFAULT '' COMMENT '二级分类id',
  `gc_id_3` char(24) NOT NULL DEFAULT '' COMMENT '三级分类id',
  `tag_name` varchar(255) NOT NULL DEFAULT '' COMMENT '分类TAG名称',
  `tag_value` text NOT NULL COMMENT '分类TAG值',
  `gc_id` char(24) NOT NULL DEFAULT '' COMMENT '商品分类id',
  `type_id` char(24) DEFAULT '' COMMENT '类型id',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_gc_tag_id` int(11) DEFAULT NULL,
  `shopnc_gc_id_1` int(11) DEFAULT NULL,
  `shopnc_gc_id_2` int(11) DEFAULT NULL,
  `shopnc_gc_id_3` int(11) DEFAULT NULL,
  `shopnc_gc_id` int(11) DEFAULT NULL,
  `shopnc_type_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品分类TAG表';

/*Table structure for table `igoods_collect` */

DROP TABLE IF EXISTS `igoods_collect`;

CREATE TABLE `igoods_collect` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `goods_id` char(24) NOT NULL DEFAULT '' COMMENT '商品ID',
  `user_id` char(24) NOT NULL DEFAULT '' COMMENT '用户ID',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关注次数',
  `collect_time` datetime NOT NULL COMMENT '关注时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品-关注商品';

/*Table structure for table `igoods_combo` */

DROP TABLE IF EXISTS `igoods_combo`;

CREATE TABLE `igoods_combo` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '推荐组合id ',
  `goods_id` char(24) NOT NULL DEFAULT '' COMMENT '主商品id',
  `goods_commonid` char(24) NOT NULL DEFAULT '' COMMENT '主商品公共id',
  `combo_goodsid` char(24) NOT NULL DEFAULT '' COMMENT '推荐组合商品id',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品推荐组合表';

/*Table structure for table `igoods_common` */

DROP TABLE IF EXISTS `igoods_common`;

CREATE TABLE `igoods_common` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '商品公共表id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '商品名称',
  `jingle` varchar(150) NOT NULL DEFAULT '' COMMENT '商品广告词',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `image` varchar(100) NOT NULL COMMENT '商品主图',
  `body` text NOT NULL COMMENT '商品内容',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品状态 0下架，1正常，10违规（禁售）',
  `verify` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品审核 1通过，0未通过，10审核中',
  `is_lock` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品锁定 0未锁，1已锁',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否热门',
  `is_new` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否最新',
  `restrict_person_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '限购人次',
  `current_period` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当期数',
  `max_period` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大期数',
  `period_goods_id` char(24) NOT NULL DEFAULT '' COMMENT '当期商品',
  `lottery_code` int(10) unsigned NOT NULL DEFAULT '10000001' COMMENT '云购码基数',
  `gc_id` char(24) NOT NULL DEFAULT '' COMMENT '商品分类',
  `gc_id_1` char(24) NOT NULL DEFAULT '' COMMENT '一级分类id',
  `gc_id_2` char(24) NOT NULL DEFAULT '' COMMENT '二级分类id',
  `gc_id_3` char(24) NOT NULL DEFAULT '' COMMENT '三级分类id',
  `gc_name` varchar(200) NOT NULL DEFAULT '' COMMENT '商品分类',
  `store_id` char(24) NOT NULL DEFAULT '' COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL DEFAULT '' COMMENT '店铺名称',
  `spec_name` varchar(255) NOT NULL DEFAULT '' COMMENT '规格名称',
  `spec_value` text NOT NULL COMMENT '规格值',
  `brand_id` char(24) NOT NULL DEFAULT '' COMMENT '品牌id',
  `brand_name` varchar(100) NOT NULL DEFAULT '' COMMENT '品牌名称',
  `type_id` char(24) DEFAULT '' COMMENT '类型id',
  `attr` text NOT NULL COMMENT '商品属性',
  `mobile_body` text NOT NULL COMMENT '手机端商品描述',
  `stateremark` varchar(255) DEFAULT '' COMMENT '违规原因',
  `verifyremark` varchar(255) DEFAULT '' COMMENT '审核失败原因',
  `addtime` datetime NOT NULL COMMENT '商品添加时间',
  `selltime` datetime NOT NULL COMMENT '上架时间',
  `specname` text NOT NULL COMMENT '规格名称序列化（下标为规格id）',
  `marketprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `costprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `discount` float unsigned NOT NULL DEFAULT '0' COMMENT '折扣',
  `serial` varchar(50) NOT NULL DEFAULT '' COMMENT '商家编号',
  `storage_alarm` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '库存报警值',
  `transport_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '运费模板',
  `transport_title` varchar(60) NOT NULL DEFAULT '' COMMENT '运费模板名称',
  `commend` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品推荐 1是，0否，默认为0',
  `collect` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数量',
  `freight` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费 0为免运费',
  `vat` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否开具增值税发票 1是，0否',
  `areaid_1` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '一级地区id',
  `areaid_2` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '二级地区id',
  `goods_stcids` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺分类id 首尾用,隔开',
  `plateid_top` int(10) unsigned DEFAULT NULL COMMENT '顶部关联板式',
  `plateid_bottom` int(10) unsigned DEFAULT NULL COMMENT '底部关联板式',
  `is_virtual` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为虚拟商品 1是，0否',
  `virtual_indate` int(10) unsigned DEFAULT NULL COMMENT '虚拟商品有效期',
  `virtual_limit` tinyint(3) unsigned DEFAULT NULL COMMENT '虚拟商品购买上限',
  `virtual_invalid_refund` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否允许过期退款， 1是，0否',
  `is_fcode` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为F码商品 1是，0否',
  `is_appoint` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否是预约商品 1是，0否',
  `appoint_satedate` int(10) unsigned NOT NULL COMMENT '预约商品出售时间',
  `is_presell` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否是预售商品 1是，0否',
  `presell_deliverdate` int(10) unsigned NOT NULL COMMENT '预售商品发货时间',
  `is_own_shop` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为平台自营',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_goods_commonid` int(11) DEFAULT NULL,
  `shopnc_gc_id` int(11) DEFAULT NULL,
  `shopnc_gc_id_1` int(11) DEFAULT NULL,
  `shopnc_gc_id_2` int(11) DEFAULT NULL,
  `shopnc_gc_id_3` int(11) DEFAULT NULL,
  `shopnc_store_id` int(11) DEFAULT NULL,
  `shopnc_brand_id` int(11) DEFAULT NULL,
  `shopnc_type_id` int(11) DEFAULT NULL,
  `shopnc_transport_id` int(11) DEFAULT NULL,
  `shopnc_areaid_1` int(11) DEFAULT NULL,
  `shopnc_areaid_2` int(11) DEFAULT NULL,
  `shopnc_plateid_top` int(11) DEFAULT NULL,
  `shopnc_plateid_bottom` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品公共内容表';

/*Table structure for table `igoods_fcode` */

DROP TABLE IF EXISTS `igoods_fcode`;

CREATE TABLE `igoods_fcode` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'F码id',
  `goods_commonid` char(24) NOT NULL DEFAULT '' COMMENT '商品公共id',
  `code` varchar(20) NOT NULL COMMENT 'F码',
  `state` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0未使用，1已使用',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_fc_id` int(11) DEFAULT NULL,
  `shopnc_goods_commonid` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品F码';

/*Table structure for table `igoods_gift` */

DROP TABLE IF EXISTS `igoods_gift`;

CREATE TABLE `igoods_gift` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '赠品id ',
  `goods_id` char(24) NOT NULL DEFAULT '' COMMENT '主商品id',
  `goods_commonid` char(24) NOT NULL DEFAULT '' COMMENT '主商品公共id',
  `goodsid` char(24) NOT NULL DEFAULT '' COMMENT '赠品商品id ',
  `goodsname` varchar(50) NOT NULL COMMENT '主商品名称',
  `goodsimage` varchar(100) NOT NULL COMMENT '主商品图片',
  `amount` tinyint(3) unsigned NOT NULL COMMENT '赠品数量',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品赠品表';

/*Table structure for table `igoods_goods` */

DROP TABLE IF EXISTS `igoods_goods`;

CREATE TABLE `igoods_goods` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '商品id(SKU)',
  `goods_commonid` char(24) NOT NULL DEFAULT '' COMMENT '商品公共表id',
  `name` varchar(50) NOT NULL COMMENT '商品名称（+规格名称）',
  `jingle` varchar(150) NOT NULL COMMENT '商品广告词',
  `price` decimal(10,2) NOT NULL COMMENT '商品价格',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '商品主图',
  `state` tinyint(3) unsigned NOT NULL COMMENT '商品状态 0下架，1正常，10违规（禁售）',
  `verify` tinyint(3) unsigned NOT NULL COMMENT '商品审核 1通过，0未通过，10审核中',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否热门',
  `is_new` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否新品',
  `period` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '期数',
  `lottery_code` int(10) unsigned NOT NULL DEFAULT '10000001' COMMENT '云购码基数',
  `lottery_prize_id` char(24) NOT NULL DEFAULT '' COMMENT '云购奖品',
  `total_person_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总需人次',
  `purchase_person_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '参与人次',
  `remain_person_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余人次',
  `complete_percent` float(5,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '完成度百分比',
  `restrict_person_time` int(9) unsigned NOT NULL DEFAULT '0' COMMENT '限购次数',
  `sale_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '销售状态 1 进行中 2 揭晓中 3 已揭晓',
  `last_purchase_time` decimal(13,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '最后购买时间',
  `prize_code` int(10) unsigned NOT NULL COMMENT '中奖码',
  `prize_time` decimal(13,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '中奖时间',
  `order_goods_list` text NOT NULL COMMENT '订单商品列表',
  `prize_buyer_id` char(24) NOT NULL DEFAULT '' COMMENT '中奖购买用户ID',
  `prize_buyer_name` varchar(30) NOT NULL DEFAULT '' COMMENT '中奖购买用户名',
  `prize_buyer_avatar` varchar(100) NOT NULL DEFAULT '' COMMENT '中奖购买用户头像',
  `prize_buyer_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '中奖购买用户注册方式',
  `prize_buyer_purchase_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '中奖购买用户购买次数',
  `prize_buyer_purchase_time` decimal(13,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '中奖购买用户购买时间',
  `prize_buyer_ip` char(15) NOT NULL DEFAULT '0.0.0.0' COMMENT '中奖购买用户IP',
  `prize_buyer_lottery_code` text NOT NULL COMMENT '中奖购买云购码',
  `prize_order_goods_id` char(24) NOT NULL DEFAULT '' COMMENT '中奖订单商品ID',
  `prize_order_goods_order_no` char(24) NOT NULL DEFAULT '' COMMENT '中奖订单NO',
  `prize_total_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '中奖购买时间总和',
  `gc_id` char(24) NOT NULL DEFAULT '' COMMENT '商品分类id',
  `gc_id_1` char(24) NOT NULL DEFAULT '' COMMENT '一级分类id',
  `gc_id_2` char(24) NOT NULL DEFAULT '' COMMENT '二级分类id',
  `gc_id_3` char(24) NOT NULL DEFAULT '' COMMENT '三级分类id',
  `store_id` char(24) NOT NULL DEFAULT '' COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL COMMENT '店铺名称',
  `brand_id` char(24) NOT NULL DEFAULT '' COMMENT '品牌id',
  `promotion_price` decimal(10,2) NOT NULL COMMENT '商品促销价格',
  `promotion_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '促销类型 0无促销，1团购，2限时折扣',
  `marketprice` decimal(10,2) NOT NULL COMMENT '市场价',
  `serial` varchar(50) NOT NULL COMMENT '商家编号',
  `storage_alarm` tinyint(3) unsigned NOT NULL COMMENT '库存报警值',
  `click` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品点击数量',
  `salenum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '销售数量',
  `collect` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数量',
  `spec` text NOT NULL COMMENT '商品规格序列化',
  `storage` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品库存',
  `addtime` datetime NOT NULL COMMENT '商品添加时间',
  `edittime` datetime NOT NULL COMMENT '商品编辑时间',
  `areaid_1` int(10) unsigned NOT NULL COMMENT '一级地区id',
  `areaid_2` int(10) unsigned NOT NULL COMMENT '二级地区id',
  `color_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '颜色规格id',
  `transport_id` mediumint(8) unsigned NOT NULL COMMENT '运费模板id',
  `freight` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费 0为免运费',
  `vat` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否开具增值税发票 1是，0否',
  `commend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '商品推荐 1是，0否 默认0',
  `stcids` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺分类id 首尾用,隔开',
  `evaluation_good_star` tinyint(3) unsigned NOT NULL DEFAULT '5' COMMENT '好评星级',
  `evaluation_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评价数',
  `is_virtual` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为虚拟商品 1是，0否',
  `virtual_indate` int(10) unsigned NOT NULL COMMENT '虚拟商品有效期',
  `virtual_limit` tinyint(3) unsigned NOT NULL COMMENT '虚拟商品购买上限',
  `virtual_invalid_refund` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否允许过期退款， 1是，0否',
  `is_fcode` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为F码商品 1是，0否',
  `is_appoint` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是预约商品 1是，0否',
  `is_presell` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是预售商品 1是，0否',
  `have_gift` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否拥有赠品',
  `is_own_shop` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为平台自营',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_goods_id` int(11) DEFAULT NULL,
  `shopnc_goods_commonid` int(11) DEFAULT NULL,
  `shopnc_store_id` int(11) DEFAULT NULL,
  `shopnc_gc_id` int(11) DEFAULT NULL,
  `shopnc_gc_id_1` int(11) DEFAULT NULL,
  `shopnc_gc_id_2` int(11) DEFAULT NULL,
  `shopnc_gc_id_3` int(11) DEFAULT NULL,
  `shopnc_brand_id` int(11) DEFAULT NULL,
  `shopnc_areaid_1` int(11) DEFAULT NULL,
  `shopnc_areaid_2` int(11) DEFAULT NULL,
  `shopnc_color_id` int(11) DEFAULT NULL,
  `shopnc_transport_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品表';

/*Table structure for table `igoods_images` */

DROP TABLE IF EXISTS `igoods_images`;

CREATE TABLE `igoods_images` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '商品图片id',
  `goods_commonid` char(24) NOT NULL DEFAULT '' COMMENT '商品公共内容id',
  `store_id` char(24) NOT NULL DEFAULT '' COMMENT '店铺id',
  `color_id` char(24) NOT NULL DEFAULT '' COMMENT '颜色规格值id',
  `image` varchar(1000) NOT NULL COMMENT '商品图片',
  `sort` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '默认主题，1是，0否',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_goods_image_id` int(11) DEFAULT NULL,
  `shopnc_goods_commonid` int(11) DEFAULT NULL,
  `shopnc_store_id` int(11) DEFAULT NULL,
  `shopnc_color_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品图片';

/*Table structure for table `igoods_spec` */

DROP TABLE IF EXISTS `igoods_spec`;

CREATE TABLE `igoods_spec` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '规格id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规格名称',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `category_id` char(24) DEFAULT '' COMMENT '所属分类id',
  `category_name` varchar(100) DEFAULT '' COMMENT '所属分类名称',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_sp_id` int(11) DEFAULT NULL,
  `shopnc_class_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品规格表';

/*Table structure for table `igoods_spec_value` */

DROP TABLE IF EXISTS `igoods_spec_value`;

CREATE TABLE `igoods_spec_value` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '规格值id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规格值名称',
  `sp_id` char(24) NOT NULL DEFAULT '' COMMENT '所属规格id',
  `gc_id` char(24) NOT NULL DEFAULT '' COMMENT '分类id',
  `store_id` char(24) DEFAULT '' COMMENT '店铺id',
  `color` varchar(10) DEFAULT '' COMMENT '规格颜色',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_sp_value_id` int(11) DEFAULT NULL,
  `shopnc_sp_id` int(11) DEFAULT NULL,
  `shopnc_gc_id` int(11) DEFAULT NULL,
  `shopnc_store_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品规格值表';

/*Table structure for table `igoods_type` */

DROP TABLE IF EXISTS `igoods_type`;

CREATE TABLE `igoods_type` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '类型id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '类型名称',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `category_id` char(24) DEFAULT '' COMMENT '所属分类id',
  `category_name` varchar(100) DEFAULT '' COMMENT '所属分类名称',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_type_id` int(11) DEFAULT NULL,
  `shopnc_class_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品类型表';

/*Table structure for table `igoods_type_brand` */

DROP TABLE IF EXISTS `igoods_type_brand`;

CREATE TABLE `igoods_type_brand` (
  `_id` char(24) NOT NULL DEFAULT '',
  `type_id` char(24) NOT NULL DEFAULT '' COMMENT '类型id',
  `brand_id` char(24) NOT NULL DEFAULT '' COMMENT '品牌id',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_type_id` int(11) DEFAULT NULL,
  `shopnc_brand_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品类型与品牌对应表';

/*Table structure for table `igoods_type_spec` */

DROP TABLE IF EXISTS `igoods_type_spec`;

CREATE TABLE `igoods_type_spec` (
  `_id` char(24) NOT NULL DEFAULT '',
  `type_id` char(24) NOT NULL DEFAULT '' COMMENT '类型id',
  `sp_id` char(24) NOT NULL DEFAULT '' COMMENT '规格id',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shopnc_type_id` int(11) DEFAULT NULL,
  `shopnc_sp_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品类型与规格对应表';

/*Table structure for table `iinvitation_invitation` */

DROP TABLE IF EXISTS `iinvitation_invitation`;

CREATE TABLE `iinvitation_invitation` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请活动',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户微信号',
  `url` varchar(300) NOT NULL DEFAULT '' COMMENT '邀请函URL',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '头像',
  `desc` text NOT NULL COMMENT '说明',
  `worth` int(10) NOT NULL DEFAULT '0' COMMENT '价值',
  `worth2` int(11) NOT NULL DEFAULT '0' COMMENT '价值2',
  `invited_num` int(11) NOT NULL DEFAULT '0' COMMENT '接受邀请次数',
  `invited_total` int(11) DEFAULT '0' COMMENT '邀请总次数限制，0为无限制',
  `send_time` datetime DEFAULT NULL COMMENT '发送时间',
  `lock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否LOCK',
  `expire` datetime NOT NULL COMMENT '锁过期时间',
  `is_need_subscribed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否需要关注',
  `subscibe_hint_url` varchar(300) NOT NULL DEFAULT '' COMMENT '关注提示页面链接',
  `personal_receive_num` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '每人领取次数限制，0为无限制',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请-发起邀请记录';

/*Table structure for table `iinvitation_invitationgotdetail` */

DROP TABLE IF EXISTS `iinvitation_invitationgotdetail`;

CREATE TABLE `iinvitation_invitationgotdetail` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请活动',
  `invitation_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请ID',
  `owner_user_id` char(50) NOT NULL DEFAULT '' COMMENT '发送邀请的用户ID',
  `owner_user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '发送邀请的用户名称',
  `owner_user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '发送邀请的用户头像',
  `got_user_id` char(50) NOT NULL DEFAULT '' COMMENT '接受邀请的用户ID',
  `got_user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '接受邀请的用户名称',
  `got_user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '接受邀请的用户头像',
  `got_time` datetime NOT NULL COMMENT '接受时间',
  `got_worth` int(11) NOT NULL DEFAULT '0' COMMENT '获取价值',
  `got_worth2` int(11) NOT NULL DEFAULT '0' COMMENT '获取价值2',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`invitation_id`),
  KEY `NewIndex2` (`got_user_id`,`activity_id`),
  KEY `NewIndex3` (`owner_user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请-接受邀请记录';

/*Table structure for table `iinvitation_rule` */

DROP TABLE IF EXISTS `iinvitation_rule`;

CREATE TABLE `iinvitation_rule` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '邀请规则ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `worth` int(11) NOT NULL DEFAULT '0' COMMENT '价值',
  `probability` int(11) NOT NULL DEFAULT '0' COMMENT '概率(N/10000)',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请-规则';

/*Table structure for table `iinvitation_user` */

DROP TABLE IF EXISTS `iinvitation_user`;

CREATE TABLE `iinvitation_user` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `worth` int(10) NOT NULL DEFAULT '0' COMMENT '价值',
  `worth2` int(11) NOT NULL DEFAULT '0' COMMENT '价值2',
  `log_time` datetime NOT NULL COMMENT '记录时间',
  `lock` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否LOCK',
  `expire` datetime NOT NULL COMMENT '锁过期时间',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请-邀请用戶';

/*Table structure for table `ilottery_exchange` */

DROP TABLE IF EXISTS `ilottery_exchange`;

CREATE TABLE `ilottery_exchange` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '抽奖ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `is_valid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效',
  `got_time` datetime NOT NULL COMMENT '获取时间',
  `source` char(10) NOT NULL DEFAULT '' COMMENT '来源',
  `prize_code` char(24) NOT NULL DEFAULT '' COMMENT '奖品信息.奖品代码',
  `prize_name` varchar(50) NOT NULL DEFAULT '' COMMENT '奖品信息.奖品名',
  `prize_category` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '奖品信息.奖品类别',
  `prize_virtual_currency` int(11) NOT NULL COMMENT '奖品信息.奖品价值',
  `prize_is_virtual` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '奖品信息.是否实物奖',
  `prize_virtual_code` char(24) NOT NULL DEFAULT '' COMMENT '券码信息.卡号',
  `prize_virtual_pwd` char(30) NOT NULL DEFAULT '' COMMENT '券码信息.卡密',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户信息.名称',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户信息.头像',
  `contact_name` varchar(50) NOT NULL DEFAULT '' COMMENT '联系信息.姓名',
  `contact_mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系信息.手机',
  `contact_address` varchar(200) NOT NULL DEFAULT '' COMMENT '联系信息.头像',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽奖-中奖';

/*Table structure for table `ilottery_limit` */

DROP TABLE IF EXISTS `ilottery_limit`;

CREATE TABLE `ilottery_limit` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '抽奖限制ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `limit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '限制数量',
  `start_time` datetime NOT NULL COMMENT '限制开始时间',
  `end_time` datetime NOT NULL COMMENT '限制结束时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽奖-限制';

/*Table structure for table `ilottery_record` */

DROP TABLE IF EXISTS `ilottery_record`;

CREATE TABLE `ilottery_record` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '抽奖记录ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `source` char(10) NOT NULL DEFAULT '' COMMENT '来源',
  `result_id` smallint(6) NOT NULL DEFAULT '0' COMMENT '结果ID',
  `result_msg` text NOT NULL COMMENT '结果说明',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽奖-日志';

/*Table structure for table `ilottery_rule` */

DROP TABLE IF EXISTS `ilottery_rule`;

CREATE TABLE `ilottery_rule` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '抽奖规则ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `allow_start_time` datetime NOT NULL COMMENT '开始时间',
  `allow_end_time` datetime NOT NULL COMMENT '结束时间',
  `allow_number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '奖品数量',
  `allow_probability` int(11) NOT NULL DEFAULT '0' COMMENT '抽奖概率',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='抽奖-规则';

/*Table structure for table `imail_settings` */

DROP TABLE IF EXISTS `imail_settings`;

CREATE TABLE `imail_settings` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `host` varchar(100) NOT NULL DEFAULT '' COMMENT 'SMTP服务器',
  `port` varchar(30) NOT NULL DEFAULT '' COMMENT 'SMTP端口',
  `address_from` varchar(30) NOT NULL DEFAULT '' COMMENT '发信人邮件地址',
  `name_from` varchar(30) NOT NULL DEFAULT '' COMMENT '发信人姓名',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT 'SMTP身份验证用户名',
  `password` varchar(30) NOT NULL DEFAULT '' COMMENT 'SMTP身份验证密码',
  `secure` char(3) NOT NULL DEFAULT 'tls' COMMENT '加密,tls或ssl',
  `is_auth` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否SMTP认证',
  `is_smtp` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否使用SMTP',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='邮件-邮件设置';

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

/*Table structure for table `iorder_cart` */

DROP TABLE IF EXISTS `iorder_cart`;

CREATE TABLE `iorder_cart` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `buyer_id` char(24) NOT NULL DEFAULT '' COMMENT '买家id',
  `store_id` char(24) NOT NULL DEFAULT '' COMMENT '店铺id',
  `store_name` varchar(50) NOT NULL DEFAULT '' COMMENT '店铺名称',
  `goods_commonid` char(24) NOT NULL DEFAULT '' COMMENT '商品公共id',
  `goods_id` char(24) NOT NULL DEFAULT '' COMMENT '商品id',
  `goods_name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '购买商品数量',
  `goods_image` varchar(100) NOT NULL DEFAULT '' COMMENT '商品图片',
  `bl_id` char(24) NOT NULL DEFAULT '' COMMENT '组合套装ID',
  `is_checkout` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已结算',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`buyer_id`,`goods_id`,`bl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='购物车表';

/*Table structure for table `iorder_common` */

DROP TABLE IF EXISTS `iorder_common`;

CREATE TABLE `iorder_common` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `order_id` char(24) NOT NULL DEFAULT '' COMMENT '订单id',
  `store_id` char(24) NOT NULL DEFAULT '' COMMENT '店铺ID',
  `shipping_time` datetime NOT NULL COMMENT '配送时间',
  `shipping_express_id` char(24) NOT NULL DEFAULT '' COMMENT '配送公司ID',
  `evaluation_time` datetime NOT NULL COMMENT '评价时间',
  `evalseller_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '卖家是否已评价买家',
  `evalseller_time` datetime NOT NULL COMMENT '卖家评价买家的时间',
  `order_message` varchar(300) DEFAULT '' COMMENT '订单留言',
  `order_pointscount` int(11) NOT NULL DEFAULT '0' COMMENT '订单赠送积分',
  `voucher_price` int(11) DEFAULT '0' COMMENT '代金券面额',
  `voucher_code` varchar(32) DEFAULT '' COMMENT '代金券编码',
  `deliver_explain` text COMMENT '发货备注',
  `daddress_id` mediumint(9) NOT NULL DEFAULT '0' COMMENT '发货地址ID',
  `receiver_name` varchar(50) NOT NULL DEFAULT '' COMMENT '收货人姓名',
  `receiver_info` varchar(500) NOT NULL DEFAULT '' COMMENT '收货人其它信息',
  `receiver_province_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '收货人省级ID',
  `receiver_city_id` mediumint(8) NOT NULL DEFAULT '0' COMMENT '收货人市级ID',
  `invoice_info` varchar(500) DEFAULT '' COMMENT '发票信息',
  `promotion_info` varchar(500) DEFAULT '' COMMENT '促销信息备注',
  `dlyo_pickup_code` varchar(4) DEFAULT '' COMMENT '提货码',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='订单信息扩展表';

/*Table structure for table `iorder_goods` */

DROP TABLE IF EXISTS `iorder_goods`;

CREATE TABLE `iorder_goods` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `order_id` char(24) NOT NULL DEFAULT '' COMMENT '订单id',
  `goods_id` char(24) NOT NULL DEFAULT '' COMMENT '商品id',
  `goods_name` varchar(50) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品购买价格',
  `goods_value` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品价值',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '商品数量',
  `goods_image` varchar(100) NOT NULL COMMENT '商品图片',
  `goods_pay_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品实际成交价',
  `store_id` char(24) NOT NULL DEFAULT '' COMMENT '店铺ID',
  `store_name` varchar(50) NOT NULL DEFAULT '' COMMENT '店铺名称',
  `buyer_id` char(24) NOT NULL DEFAULT '' COMMENT '买家ID',
  `buyer_name` varchar(30) NOT NULL DEFAULT '' COMMENT '买家姓名',
  `buyer_mobile` char(20) NOT NULL DEFAULT '' COMMENT '买家手机',
  `buyer_email` varchar(30) NOT NULL DEFAULT '' COMMENT '买家邮箱',
  `buyer_avatar` varchar(100) NOT NULL DEFAULT '' COMMENT '买家头像',
  `buyer_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '买家注册方式',
  `buyer_ip` char(15) NOT NULL DEFAULT '0.0.0.0' COMMENT '买家IP',
  `goods_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1默认2团购商品3限时折扣商品4组合套装5赠品',
  `promotions_id` char(24) NOT NULL DEFAULT '' COMMENT '促销活动ID（团购ID/限时折扣ID/优惠套装ID）与goods_type搭配使用',
  `commis_rate` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '佣金比例',
  `gc_id` char(24) NOT NULL DEFAULT '' COMMENT '商品最底级分类ID',
  `goods_commonid` char(24) NOT NULL DEFAULT '' COMMENT '商品公共ID',
  `goods_period` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '期数',
  `goods_total_person_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总次数',
  `goods_remain_person_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '剩余次数',
  `is_success` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否成功购买',
  `failure_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '失败购买次数',
  `lottery_prize_id` char(24) NOT NULL DEFAULT '' COMMENT '云购奖品',
  `lottery_code` text NOT NULL COMMENT '云购码',
  `purchase_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '成功购码次数',
  `purchase_time` decimal(13,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '云购时间',
  `prize_code` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '中奖码',
  `prize_time` decimal(13,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '揭晓时间',
  `prize_buyer_id` char(24) NOT NULL DEFAULT '' COMMENT '中奖购买用户ID',
  `prize_buyer_name` varchar(30) NOT NULL DEFAULT '' COMMENT '中奖购买用户名',
  `prize_buyer_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '中奖购买用户注册方式',
  `prize_order_goods_id` char(24) NOT NULL DEFAULT '' COMMENT '中奖订单商品ID',
  `refund_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退购次数',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 1 进行中 2 揭晓中 3 已揭晓 4已退购',
  `order_no` char(24) NOT NULL DEFAULT '' COMMENT '新订单编号',
  `order_state` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '新订单状态',
  `post_id` char(24) NOT NULL DEFAULT '' COMMENT '晒单ID',
  `is_post_single` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否晒单',
  `is_send_msg` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已发送消息',
  `orderActDesc` text NOT NULL COMMENT '订单备注',
  `consignee_info` text NOT NULL COMMENT '收货人详细信息',
  `delivery_info` text NOT NULL COMMENT '发货详细信息',
  `order_message` text NOT NULL COMMENT '订单备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`order_id`),
  KEY `NewIndex2` (`goods_id`),
  KEY `NewIndex3` (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='订单商品表';

/*Table structure for table `iorder_log` */

DROP TABLE IF EXISTS `iorder_log`;

CREATE TABLE `iorder_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `order_id` char(24) NOT NULL DEFAULT '' COMMENT '订单id',
  `order_state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态',
  `log_time` datetime NOT NULL COMMENT '处理时间',
  `msg` text NOT NULL COMMENT '文字描述',
  `role` char(20) NOT NULL DEFAULT '' COMMENT '操作人角色',
  `user_id` char(24) NOT NULL DEFAULT '' COMMENT '操作人ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '操作人名称',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='订单处理历史表';

/*Table structure for table `iorder_order` */

DROP TABLE IF EXISTS `iorder_order`;

CREATE TABLE `iorder_order` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '订单ID',
  `order_sn` char(24) NOT NULL DEFAULT '' COMMENT '订单编号',
  `pay_sn` char(24) NOT NULL DEFAULT '' COMMENT '支付单号',
  `store_id` char(24) NOT NULL DEFAULT '' COMMENT '卖家店铺id',
  `store_name` varchar(50) NOT NULL DEFAULT '' COMMENT '卖家店铺名称',
  `buyer_id` char(24) NOT NULL DEFAULT '' COMMENT '买家id',
  `buyer_name` varchar(50) NOT NULL DEFAULT '' COMMENT '买家姓名',
  `buyer_email` varchar(80) NOT NULL DEFAULT '' COMMENT '买家电子邮箱',
  `buyer_mobile` char(20) NOT NULL DEFAULT '' COMMENT '买家手机',
  `buyer_avatar` varchar(100) NOT NULL DEFAULT '' COMMENT '买家头像',
  `buyer_register_by` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '买家注册方式',
  `add_time` datetime NOT NULL COMMENT '订单生成时间',
  `payment_code` char(10) NOT NULL DEFAULT '' COMMENT '支付方式名称代码',
  `payment_time` datetime NOT NULL COMMENT '支付(付款)时间',
  `finished_time` datetime NOT NULL COMMENT '订单完成时间',
  `goods_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品总金额',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
  `rcb_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值卡支付金额',
  `pd_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款支付金额',
  `points_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '积分支付金额',
  `shipping_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费',
  `evaluation_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '评价状态 0未评价，1已评价，2已过期未评价',
  `order_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;',
  `refund_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态:0是无退款,1是部分退款,2是全部退款',
  `lock_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '锁定状态:0是正常,大于0是锁定,默认是0',
  `delete_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除状态0未删除1放入回收站2彻底删除',
  `refund_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `delay_time` datetime DEFAULT NULL COMMENT '延迟时间',
  `order_from` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1WEB 2mobile',
  `shipping_code` varchar(50) NOT NULL DEFAULT '' COMMENT '物流单号',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`order_sn`),
  KEY `NewIndex2` (`pay_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='订单表';

/*Table structure for table `iorder_pay` */

DROP TABLE IF EXISTS `iorder_pay`;

CREATE TABLE `iorder_pay` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `pay_sn` char(24) NOT NULL DEFAULT '' COMMENT '支付单号',
  `buyer_id` char(24) NOT NULL DEFAULT '' COMMENT '买家ID',
  `api_pay_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0默认未支付1已支付(只有第三方支付接口通知到时才会更改此状态)',
  `process_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否处理完成',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
  `goods_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品总金额',
  `rcb_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值卡支付总金额',
  `pd_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款支付总金额',
  `points_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '福分支付总金额',
  `shipping_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费总金额',
  `refund_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `pay_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '支付总金额',
  `payment_code` char(24) NOT NULL DEFAULT '' COMMENT '支付方式名称代码',
  `payment_time` datetime NOT NULL COMMENT '支付(付款)时间',
  `success_count` int(11) NOT NULL DEFAULT '0' COMMENT '成功数量',
  `failure_count` int(11) NOT NULL DEFAULT '0' COMMENT '失败数量',
  `is_pd_used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用预付款',
  `is_points_used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用福分',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `buyer_name` varchar(50) NOT NULL DEFAULT '' COMMENT '买家姓名',
  `process_task` varchar(30) NOT NULL DEFAULT '' COMMENT '处理内容',
  `memo` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`_id`),
  KEY `NewIndex2` (`buyer_id`),
  KEY `NewIndex1` (`pay_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='订单支付表';

/*Table structure for table `iorder_statistics` */

DROP TABLE IF EXISTS `iorder_statistics`;

CREATE TABLE `iorder_statistics` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `order_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
  `goods_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '商品总金额',
  `rcb_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '充值卡总金额',
  `pd_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '预存款总金额',
  `points_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '福分总金额',
  `shipping_fee` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费总金额',
  `refund_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `pay_amount` decimal(15,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '支付总金额',
  `success_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '成功数量',
  `failure_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '失败数量',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单-统计';

/*Table structure for table `ipayment_log` */

DROP TABLE IF EXISTS `ipayment_log`;

CREATE TABLE `ipayment_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '日志ID',
  `user_id` char(24) NOT NULL DEFAULT '' COMMENT '用户ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付类型 0:全部 1:充值 2:消费 3:转账',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `log_time` datetime NOT NULL COMMENT '记录时间',
  `desc` varchar(50) NOT NULL DEFAULT '' COMMENT '支付说明',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付-日志';

/*Table structure for table `ipayment_notify` */

DROP TABLE IF EXISTS `ipayment_notify`;

CREATE TABLE `ipayment_notify` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '通知ID',
  `out_trade_no` char(24) NOT NULL DEFAULT '' COMMENT '支付单号',
  `content` text NOT NULL COMMENT '通知内容',
  `notify_time` datetime NOT NULL COMMENT '通知时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`out_trade_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付-通知';

/*Table structure for table `ipayment_payment` */

DROP TABLE IF EXISTS `ipayment_payment`;

CREATE TABLE `ipayment_payment` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '支付索引id',
  `code` char(20) NOT NULL DEFAULT '' COMMENT '支付代码名称',
  `name` char(20) NOT NULL DEFAULT '' COMMENT '支付名称',
  `config` text NOT NULL COMMENT '支付接口配置信息',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '接口状态0禁用1启用',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付方式表';

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

/*Table structure for table `ipredeposit_cash` */

DROP TABLE IF EXISTS `ipredeposit_cash`;

CREATE TABLE `ipredeposit_cash` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `member_id` char(24) NOT NULL DEFAULT '' COMMENT '会员编号',
  `member_name` varchar(50) NOT NULL DEFAULT '' COMMENT '会员名称',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `bank_name` varchar(40) NOT NULL DEFAULT '' COMMENT '收款银行',
  `bank_no` varchar(30) NOT NULL DEFAULT '' COMMENT '收款账号',
  `bank_user` varchar(10) NOT NULL DEFAULT '' COMMENT '开户人姓名',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `payment_time` datetime NOT NULL COMMENT '付款时间',
  `payment_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '提现支付状态 0默认1支付完成',
  `payment_admin` varchar(30) NOT NULL DEFAULT '' COMMENT '支付管理员',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='预存款提现记录表';

/*Table structure for table `ipredeposit_log` */

DROP TABLE IF EXISTS `ipredeposit_log`;

CREATE TABLE `ipredeposit_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `member_id` char(24) NOT NULL DEFAULT '' COMMENT '会员编号',
  `member_name` varchar(50) NOT NULL DEFAULT '' COMMENT '会员名称',
  `admin_name` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员名称',
  `type` varchar(15) NOT NULL DEFAULT '' COMMENT 'order_pay下单支付预存款,order_freeze下单冻结预存款,order_cancel取消订单解冻预存款,order_comb_pay下单支付被冻结的预存款,recharge充值,cash_apply申请提现冻结预存款,cash_pay提现成功,cash_del取消提现申请，解冻预存款,refund退款',
  `av_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '可用金额变更0表示未变更',
  `freeze_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '冻结金额变更0表示未变更',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `desc` varchar(150) DEFAULT NULL COMMENT '描述',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='预存款变更日志表';

/*Table structure for table `ipredeposit_recharge` */

DROP TABLE IF EXISTS `ipredeposit_recharge`;

CREATE TABLE `ipredeposit_recharge` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `member_id` char(24) NOT NULL DEFAULT '' COMMENT '会员编号',
  `member_name` varchar(50) DEFAULT '' COMMENT '会员名称',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `payment_code` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式',
  `payment_name` varchar(15) NOT NULL DEFAULT '' COMMENT '支付方式',
  `trade_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方支付接口交易号',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `payment_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态 0未支付1支付',
  `payment_time` datetime DEFAULT NULL COMMENT '支付时间',
  `admin` varchar(30) DEFAULT '' COMMENT '管理员名',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `pay_sn` char(24) NOT NULL DEFAULT '' COMMENT '支付单号',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='预存款充值表';

/*Table structure for table `iprize_category` */

DROP TABLE IF EXISTS `iprize_category`;

CREATE TABLE `iprize_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `code` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类码',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='奖品-奖品类别';

/*Table structure for table `iprize_code` */

DROP TABLE IF EXISTS `iprize_code`;

CREATE TABLE `iprize_code` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '活动ID',
  `prize_id` char(24) NOT NULL DEFAULT '' COMMENT '奖品ID',
  `code` char(30) NOT NULL DEFAULT '' COMMENT '虚拟卡编号',
  `pwd` char(30) NOT NULL DEFAULT '' COMMENT '虚拟卡密码',
  `is_used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用',
  `start_time` datetime NOT NULL COMMENT '开始有效期',
  `end_time` datetime NOT NULL COMMENT '结束有效期',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `index1` (`prize_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='奖品-券码';

/*Table structure for table `iprize_prize` */

DROP TABLE IF EXISTS `iprize_prize`;

CREATE TABLE `iprize_prize` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `prize_name` varchar(50) NOT NULL DEFAULT '' COMMENT '奖品名',
  `prize_code` char(24) NOT NULL DEFAULT '' COMMENT '奖品代码',
  `is_virtual` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是虚拟奖品',
  `virtual_currency` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟价值',
  `is_need_virtual_code` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否发放奖品券码',
  `is_valid` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否立即生效',
  `category` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '奖品类别',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='奖品-奖品';

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

/*Table structure for table `isms_settings` */

DROP TABLE IF EXISTS `isms_settings`;

CREATE TABLE `isms_settings` (
  `_id` char(24) NOT NULL DEFAULT '',
  `apiname` varchar(30) NOT NULL DEFAULT '' COMMENT '短信接口',
  `apikey` char(50) NOT NULL DEFAULT '' COMMENT '用户唯一标识',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='短信设置表';

/*Table structure for table `istore_store` */

DROP TABLE IF EXISTS `istore_store`;

CREATE TABLE `istore_store` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '店铺ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '店铺名称',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '店铺状态，0关闭，1开启，2审核中',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='店铺表';

/*Table structure for table `itask_log` */

DROP TABLE IF EXISTS `itask_log`;

CREATE TABLE `itask_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `task` varchar(50) NOT NULL DEFAULT '' COMMENT '任务名',
  `is_success` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否成功',
  `request` text NOT NULL COMMENT '请求参数',
  `result` text NOT NULL COMMENT '获得结果',
  `log_time` datetime NOT NULL COMMENT '日志时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='任务-日志';

/*Table structure for table `itencent_appkey` */

DROP TABLE IF EXISTS `itencent_appkey`;

CREATE TABLE `itencent_appkey` (
  `_id` char(24) NOT NULL DEFAULT '',
  `appName` varchar(30) NOT NULL DEFAULT '',
  `akey` char(50) NOT NULL DEFAULT '',
  `skey` char(50) NOT NULL DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `itencent_application` */

DROP TABLE IF EXISTS `itencent_application`;

CREATE TABLE `itencent_application` (
  `_id` char(24) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '应用名',
  `appKeyId` char(50) NOT NULL DEFAULT '' COMMENT '应用密钥',
  `secretKey` char(50) NOT NULL DEFAULT '' COMMENT '秘钥',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='腾讯-应用设置';

/*Table structure for table `itencent_oauthinfo` */

DROP TABLE IF EXISTS `itencent_oauthinfo`;

CREATE TABLE `itencent_oauthinfo` (
  `_id` char(24) NOT NULL DEFAULT '',
  `applicationId` char(24) NOT NULL DEFAULT '' COMMENT '应用',
  `access_token` char(150) NOT NULL DEFAULT '' COMMENT 'AccessToken',
  `expire_in` int(11) NOT NULL DEFAULT '0' COMMENT '生命周期',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='腾讯-授权信息';

/*Table structure for table `itencent_user` */

DROP TABLE IF EXISTS `itencent_user`;

CREATE TABLE `itencent_user` (
  `_id` char(24) NOT NULL DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='腾讯-用户';

/*Table structure for table `ivote_category` */

DROP TABLE IF EXISTS `ivote_category`;

CREATE TABLE `ivote_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '类别ID',
  `code` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型值',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '类型名称',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投票-类型';

/*Table structure for table `ivote_item` */

DROP TABLE IF EXISTS `ivote_item`;

CREATE TABLE `ivote_item` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '选项ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `desc` text NOT NULL COMMENT '说明',
  `subject_id` char(24) NOT NULL DEFAULT '' COMMENT '所属主题',
  `vote_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票次数',
  `is_closed` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否关闭',
  `show_order` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序(从大到小)',
  `rank_period` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '排行期数',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投票-选项';

/*Table structure for table `ivote_limit` */

DROP TABLE IF EXISTS `ivote_limit`;

CREATE TABLE `ivote_limit` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '限制ID',
  `category` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '限制类别',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `limit_count` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '限制次数',
  `activity` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `subject` char(24) NOT NULL DEFAULT '' COMMENT '主题',
  `item` char(24) NOT NULL DEFAULT '' COMMENT '选项',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投票-限制';

/*Table structure for table `ivote_limit_category` */

DROP TABLE IF EXISTS `ivote_limit_category`;

CREATE TABLE `ivote_limit_category` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '限制类别ID',
  `category` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '限制类别值',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名称',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投票-限制类别';

/*Table structure for table `ivote_log` */

DROP TABLE IF EXISTS `ivote_log`;

CREATE TABLE `ivote_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '明细ID',
  `activity` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `subject` char(24) NOT NULL DEFAULT '' COMMENT '投票主题',
  `item` char(24) NOT NULL DEFAULT '' COMMENT '选项',
  `vote_time` datetime NOT NULL COMMENT '投票时间',
  `identity` char(50) NOT NULL DEFAULT '' COMMENT '投票凭证',
  `ip` char(15) NOT NULL DEFAULT '' COMMENT 'IP',
  `session_id` char(30) NOT NULL DEFAULT '' COMMENT '会话ID',
  `vote_num` smallint(1) unsigned NOT NULL DEFAULT '1' COMMENT '投票次数',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投票-明细';

/*Table structure for table `ivote_period` */

DROP TABLE IF EXISTS `ivote_period`;

CREATE TABLE `ivote_period` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '排行期ID',
  `subject_id` char(24) NOT NULL DEFAULT '' COMMENT '投票主题ID',
  `period` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '当前期数',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投票-排行期';

/*Table structure for table `ivote_rank_period` */

DROP TABLE IF EXISTS `ivote_rank_period`;

CREATE TABLE `ivote_rank_period` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '每期排行ID',
  `subject_id` char(24) NOT NULL DEFAULT '' COMMENT '投票主题ID',
  `period` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '期数',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `desc` text NOT NULL COMMENT '详细',
  `vote_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票数',
  `show_order` smallint(1) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投票-每期排行';

/*Table structure for table `ivote_subject` */

DROP TABLE IF EXISTS `ivote_subject`;

CREATE TABLE `ivote_subject` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '主题ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '主题名',
  `desc` text NOT NULL COMMENT '描述',
  `vote_category` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '投票类型',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `is_closed` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否关闭',
  `vote_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '投票次数',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '所属活动',
  `show_order` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序(从大到小)',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='投票-主题';

/*Table structure for table `iweixin_application` */

DROP TABLE IF EXISTS `iweixin_application`;

CREATE TABLE `iweixin_application` (
  `_id` char(24) NOT NULL DEFAULT '',
  `weixin_name` varchar(30) NOT NULL DEFAULT '',
  `weixin_id` char(20) NOT NULL DEFAULT '',
  `verify_token` char(50) NOT NULL DEFAULT '',
  `appid` char(20) NOT NULL DEFAULT '',
  `secret` char(45) NOT NULL DEFAULT '',
  `mch_id` char(10) DEFAULT '',
  `sub_mch_id` char(15) DEFAULT '',
  `key` char(50) DEFAULT '',
  `cert` binary(1) DEFAULT NULL,
  `certKey` binary(1) DEFAULT NULL,
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

/*Table structure for table `iweixinredpack_customer` */

DROP TABLE IF EXISTS `iweixinredpack_customer`;

CREATE TABLE `iweixinredpack_customer` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '客户名称',
  `nick_name` varchar(50) NOT NULL DEFAULT '' COMMENT '提供方名称',
  `send_name` varchar(50) NOT NULL DEFAULT '' COMMENT '商户名称',
  `total_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总金额(分)',
  `used_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已使用金额(分)',
  `remain_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余金额(分)',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信红包-客户';

/*Table structure for table `iweixinredpack_got_log` */

DROP TABLE IF EXISTS `iweixinredpack_got_log`;

CREATE TABLE `iweixinredpack_got_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `mch_billno` char(30) NOT NULL DEFAULT '' COMMENT '商户订单号',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '红包用户ID',
  `re_openid` char(50) NOT NULL DEFAULT '' COMMENT '用户openid',
  `re_nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '微信昵称',
  `re_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '微信头像',
  `client_ip` char(15) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `activity` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `customer` char(24) NOT NULL DEFAULT '' COMMENT '客户',
  `redpack` char(24) NOT NULL DEFAULT '' COMMENT '红包',
  `total_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '数量',
  `total_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金额(分)',
  `got_time` datetime NOT NULL COMMENT '领取时间',
  `isOK` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否OK',
  `try_count` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '重试次数',
  `is_reissue` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否补发',
  `isNeedSendRedpack` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否正式发送',
  `error_logs` text NOT NULL COMMENT '错误日志记录',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信红包-发放记录';

/*Table structure for table `iweixinredpack_limit` */

DROP TABLE IF EXISTS `iweixinredpack_limit`;

CREATE TABLE `iweixinredpack_limit` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `customer` char(24) NOT NULL DEFAULT '' COMMENT '客户',
  `redpack` char(24) NOT NULL DEFAULT '' COMMENT '红包',
  `personal_got_num_limit` smallint(1) unsigned NOT NULL DEFAULT '1' COMMENT '每人获取数量限制',
  `start_time` datetime NOT NULL COMMENT '限制开始时间',
  `end_time` datetime NOT NULL COMMENT '限制结束时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信红包-活动规则限制';

/*Table structure for table `iweixinredpack_redpack` */

DROP TABLE IF EXISTS `iweixinredpack_redpack`;

CREATE TABLE `iweixinredpack_redpack` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `code` char(24) NOT NULL DEFAULT '' COMMENT '红包代码',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '红包名',
  `desc` text NOT NULL COMMENT '说明',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='微信红包-红包';

/*Table structure for table `iweixinredpack_reissue` */

DROP TABLE IF EXISTS `iweixinredpack_reissue`;

CREATE TABLE `iweixinredpack_reissue` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `logid` char(24) NOT NULL DEFAULT '' COMMENT '红包日志ID',
  `redpack` text NOT NULL COMMENT '红包日志',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信红包-补发日志';

/*Table structure for table `iweixinredpack_rule` */

DROP TABLE IF EXISTS `iweixinredpack_rule`;

CREATE TABLE `iweixinredpack_rule` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `activity` char(24) NOT NULL DEFAULT '' COMMENT '活动',
  `customer` char(24) NOT NULL DEFAULT '' COMMENT '客户',
  `redpack` char(24) NOT NULL DEFAULT '' COMMENT '红包',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '红包发放总金额(分)',
  `quantity` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '红包发放总数量',
  `min_cash` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最小金额(分)',
  `max_cash` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大金额(分)',
  `allow_probability` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '概率(N/10000)',
  `personal_can_get_num` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '最大数量(人)',
  `nick_name` varchar(50) NOT NULL DEFAULT '' COMMENT '提供方名称',
  `send_name` varchar(50) NOT NULL DEFAULT '' COMMENT '商户名称',
  `wishing` varchar(300) NOT NULL DEFAULT '' COMMENT '红包祝福',
  `remark` varchar(300) NOT NULL DEFAULT '' COMMENT '备注',
  `logo_imgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '商户logo',
  `share_content` varchar(300) NOT NULL DEFAULT '' COMMENT '分享文案',
  `share_url` varchar(300) NOT NULL DEFAULT '' COMMENT '分享链接 ',
  `share_imgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '分享的图片',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信红包-红包发放规则';

/*Table structure for table `menu` */

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `_id` char(24) NOT NULL DEFAULT '',
  `pid` char(24) DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  `show_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `url` varchar(100) DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `role` */

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `_id` char(24) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  `alias` varchar(30) NOT NULL DEFAULT '',
  `desc` varchar(100) NOT NULL DEFAULT '',
  `menu_list` text NOT NULL,
  `operation_list` text NOT NULL,
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `source` */

DROP TABLE IF EXISTS `source`;

CREATE TABLE `source` (
  `_id` char(24) NOT NULL DEFAULT '',
  `key` varchar(30) NOT NULL DEFAULT '',
  `value` char(10) NOT NULL DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `_id` char(24) NOT NULL DEFAULT '',
  `username` varchar(30) NOT NULL DEFAULT '',
  `password` char(20) NOT NULL DEFAULT '',
  `lastip` char(20) DEFAULT '',
  `lasttime` datetime DEFAULT NULL,
  `times` int(11) unsigned DEFAULT '0',
  `role` char(24) NOT NULL DEFAULT '',
  `__CREATE_TIME__` datetime NOT NULL,
  `__MODIFY_TIME__` datetime NOT NULL,
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
