/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.6.35 : Database - webcms
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
  `goods_price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品价格',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='购物车表';

/*Data for the table `iorder_cart` */

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='订单信息扩展表';

/*Data for the table `iorder_common` */

/*Table structure for table `iorder_goods` */

DROP TABLE IF EXISTS `iorder_goods`;

CREATE TABLE `iorder_goods` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `order_id` char(24) NOT NULL DEFAULT '' COMMENT '订单id',
  `goods_id` char(24) NOT NULL DEFAULT '' COMMENT '商品id',
  `goods_name` varchar(50) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品购买价格',
  `goods_value` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品价值',
  `goods_num` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '商品数量',
  `goods_image` varchar(100) NOT NULL COMMENT '商品图片',
  `goods_pay_price` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品实际成交价',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='订单商品表';

/*Data for the table `iorder_goods` */

/*Table structure for table `iorder_log` */

DROP TABLE IF EXISTS `iorder_log`;

CREATE TABLE `iorder_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `order_id` char(24) NOT NULL DEFAULT '' COMMENT '订单id',
  `order_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='订单处理历史表';

/*Data for the table `iorder_log` */

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
  `goods_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品总金额',
  `order_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单总金额',
  `rcb_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '充值卡支付金额',
  `pd_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '预存款支付金额',
  `points_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '积分支付金额',
  `shipping_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '运费',
  `evaluation_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '评价状态 0未评价，1已评价，2已过期未评价',
  `order_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;',
  `refund_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态:0是无退款,1是部分退款,2是全部退款',
  `lock_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '锁定状态:0是正常,大于0是锁定,默认是0',
  `delete_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除状态0未删除1放入回收站2彻底删除',
  `refund_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退款金额',
  `delay_time` datetime DEFAULT NULL COMMENT '延迟时间',
  `order_from` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1WEB 2mobile',
  `shipping_code` varchar(50) NOT NULL DEFAULT '' COMMENT '物流单号',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`order_sn`),
  KEY `NewIndex2` (`pay_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='订单表';

/*Data for the table `iorder_order` */

/*Table structure for table `iorder_pay` */

DROP TABLE IF EXISTS `iorder_pay`;

CREATE TABLE `iorder_pay` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `pay_sn` char(24) NOT NULL DEFAULT '' COMMENT '支付单号',
  `buyer_id` char(24) NOT NULL DEFAULT '' COMMENT '买家ID',
  `api_pay_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0默认未支付1已支付(只有第三方支付接口通知到时才会更改此状态)',
  `process_state` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否处理完成',
  `order_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单总金额',
  `goods_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品总金额',
  `rcb_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '充值卡支付总金额',
  `pd_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '预存款支付总金额',
  `points_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '福分支付总金额',
  `shipping_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '运费总金额',
  `refund_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退款金额',
  `pay_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付总金额',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='订单支付表';

/*Data for the table `iorder_pay` */

/*Table structure for table `iorder_statistics` */

DROP TABLE IF EXISTS `iorder_statistics`;

CREATE TABLE `iorder_statistics` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT 'ID',
  `order_amount` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '订单总金额',
  `goods_amount` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '商品总金额',
  `rcb_amount` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '充值卡总金额',
  `pd_amount` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '预存款总金额',
  `points_amount` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '福分总金额',
  `shipping_fee` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '运费总金额',
  `refund_amount` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '退款金额',
  `pay_amount` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '支付总金额',
  `success_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '成功数量',
  `failure_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '失败数量',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单-统计';

/*Data for the table `iorder_statistics` */

insert  into `iorder_statistics`(`_id`,`order_amount`,`goods_amount`,`rcb_amount`,`pd_amount`,`points_amount`,`shipping_fee`,`refund_amount`,`pay_amount`,`success_count`,`failure_count`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('568149f3887c226e6a8b56fd',0,0,0,0,0,0,0,0,0,0,'2015-12-28 22:40:51','2016-01-17 11:39:02',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
