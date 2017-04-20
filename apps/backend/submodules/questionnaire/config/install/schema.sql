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

/*Table structure for table `iquestionnaire_answer` */

DROP TABLE IF EXISTS `iquestionnaire_answer`;

CREATE TABLE `iquestionnaire_answer` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '答题ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `user_headimgurl` varchar(300) NOT NULL DEFAULT '' COMMENT '用户头像',
  `questionnaire_id` char(24) NOT NULL DEFAULT '' COMMENT '问卷ID',
  `random_id` char(24) NOT NULL DEFAULT '' COMMENT '随机问卷编号',
  `answer_list` text NOT NULL COMMENT '用户答题',
  `score` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '得分',
  `question_num` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '题目数量',
  `correct_num` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '正确数量',
  `wrong_num` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '错误数量',
  `noanswer_num` smallint(2) unsigned NOT NULL DEFAULT '0' COMMENT '未答题数量',
  `answer_time` datetime NOT NULL COMMENT '答题时间',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='问卷-答题';

/*Data for the table `iquestionnaire_answer` */

insert  into `iquestionnaire_answer`(`_id`,`user_id`,`user_name`,`user_headimgurl`,`questionnaire_id`,`random_id`,`answer_list`,`score`,`question_num`,`correct_num`,`wrong_num`,`noanswer_num`,`answer_time`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('58b65ed389972ff2008b4567','xxx','guoyongrong','xxx','58af94ff6a2e0e0b008b4567','','[{\"question_id\":\"58b64bda89972ff1008b4567\",\"answers\":[{\"item_id\":\"58b64bf589972fef008b4567\",\"key\":\"A\",\"content\":\"\"},{\"item_id\":\"58b64c0989972ff3008b4567\",\"key\":\"B\",\"content\":\"\"},{\"item_id\":\"58b64c1b89972fee008b4567\",\"key\":\"C\",\"content\":\"\\u6d77\\u5357\"}],\"result\":true,\"score\":2,\"wrong_num\":0,\"correct_num\":1},{\"question_id\":\"58afa31a6a2e0e00018b4567\",\"answers\":[{\"item_id\":\"58afa85e6a2e0e01018b4568\",\"key\":\"A\",\"content\":\"\"}],\"result\":true,\"score\":1,\"wrong_num\":0,\"correct_num\":1}]',3,2,2,0,0,'2017-03-01 13:40:35','','2017-03-01 13:40:35','2017-03-01 13:40:35',0);

/*Table structure for table `iquestionnaire_question` */

DROP TABLE IF EXISTS `iquestionnaire_question`;

CREATE TABLE `iquestionnaire_question` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '题目ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '题目名',
  `questionnaire_id` char(24) NOT NULL DEFAULT '' COMMENT '所属问卷',
  `question_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '题目题型',
  `is_required` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否必填',
  `content` text NOT NULL COMMENT '题目内容',
  `correct_answer` varchar(20) NOT NULL DEFAULT '' COMMENT '正确答案,逗号分隔',
  `correct_hint` text NOT NULL COMMENT '正确提示',
  `next_question_id` char(24) NOT NULL DEFAULT '' COMMENT '下一题',
  `picture` varchar(300) NOT NULL DEFAULT '' COMMENT '图片',
  `score` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '分数',
  `show_order` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序(从大到小)',
  `correct_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '正确次数',
  `wrong_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '错误次数',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='问卷-题目';

/*Data for the table `iquestionnaire_question` */

insert  into `iquestionnaire_question`(`_id`,`name`,`questionnaire_id`,`question_type`,`is_required`,`content`,`correct_answer`,`correct_hint`,`next_question_id`,`picture`,`score`,`show_order`,`correct_times`,`wrong_times`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('58afa31a6a2e0e00018b4567','题目1','58af94ff6a2e0e0b008b4567',1,1,'你喜欢的城市有哪个？','A','北京','','',1,0,1,0,'','2017-02-24 11:06:02','2017-03-01 13:40:35',0),('58b64bda89972ff1008b4567','题目2','58af94ff6a2e0e0b008b4567',2,0,'你去过的地方有哪些？','A,B,C','','','',2,0,1,0,'','2017-03-01 12:19:37','2017-03-01 13:40:35',0);

/*Table structure for table `iquestionnaire_question_item` */

DROP TABLE IF EXISTS `iquestionnaire_question_item`;

CREATE TABLE `iquestionnaire_question_item` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '题目选项ID',
  `question_id` char(24) NOT NULL DEFAULT '' COMMENT '所属题目',
  `key` varchar(50) NOT NULL DEFAULT '' COMMENT '选项',
  `content` text NOT NULL COMMENT '内容',
  `score` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '分数',
  `is_other` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否其他',
  `used_times` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '被选次数',
  `show_order` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序(从大到小)',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='问卷-题目选项';

/*Data for the table `iquestionnaire_question_item` */

insert  into `iquestionnaire_question_item`(`_id`,`question_id`,`key`,`content`,`score`,`is_other`,`used_times`,`show_order`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('58afa85e6a2e0e01018b4568','58afa31a6a2e0e00018b4567','A','北京',0,0,1,0,'','2017-02-24 11:28:30','2017-03-01 13:40:35',0),('58b6498489972f0d008b4567','58afa31a6a2e0e00018b4567','B','上海',0,0,0,0,'','2017-03-01 12:09:40','2017-03-01 12:09:40',0),('58b64bf589972fef008b4567','58b64bda89972ff1008b4567','A','广州',0,0,1,0,'','2017-03-01 12:20:05','2017-03-01 13:40:35',0),('58b64c0989972ff3008b4567','58b64bda89972ff1008b4567','B','福州',0,0,1,0,'','2017-03-01 12:20:25','2017-03-01 13:40:35',0),('58b64c1b89972fee008b4567','58b64bda89972ff1008b4567','C','其他',0,1,1,0,'','2017-03-01 12:20:43','2017-03-01 13:40:35',0);

/*Table structure for table `iquestionnaire_question_type` */

DROP TABLE IF EXISTS `iquestionnaire_question_type`;

CREATE TABLE `iquestionnaire_question_type` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '题目类型ID',
  `code` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分类值',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '分类名',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='问卷-题目类型';

/*Data for the table `iquestionnaire_question_type` */

insert  into `iquestionnaire_question_type`(`_id`,`code`,`name`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('58aef15b06dfa909008b4567',1,'单选题','2017-02-23 22:27:39','2017-02-23 22:27:39',0),('58aef16e06dfa9ea008b4567',2,'多选题','2017-02-23 22:27:58','2017-02-23 22:27:58',0),('58aef17f06dfa9f3008b4567',3,'简答题','2017-02-23 22:28:15','2017-02-23 22:28:15',0);

/*Table structure for table `iquestionnaire_questionnaire` */

DROP TABLE IF EXISTS `iquestionnaire_questionnaire`;

CREATE TABLE `iquestionnaire_questionnaire` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '问卷ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '问卷名',
  `start_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '截止时间',
  `is_rand` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否随机问卷',
  `rand_number` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '随机问卷题目数量',
  `show_order` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序(从大到小)',
  `activity_id` char(24) NOT NULL DEFAULT '' COMMENT '所属活动',
  `memo` text NOT NULL COMMENT '备注',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='问卷-问卷';

/*Data for the table `iquestionnaire_questionnaire` */

insert  into `iquestionnaire_questionnaire`(`_id`,`name`,`start_time`,`end_time`,`is_rand`,`rand_number`,`show_order`,`activity_id`,`memo`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('58af94ff6a2e0e0b008b4567','问卷1','2017-02-24 00:00:00','2019-11-29 10:55:07',0,0,1,'58af94a36a2e0e01018b4567','','2017-02-24 10:05:51','2017-02-24 10:05:51',0);

/*Table structure for table `iquestionnaire_random` */

DROP TABLE IF EXISTS `iquestionnaire_random`;

CREATE TABLE `iquestionnaire_random` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '随机题库ID',
  `user_id` char(50) NOT NULL DEFAULT '' COMMENT '用户ID',
  `questionnaire_id` char(24) NOT NULL DEFAULT '' COMMENT '问卷ID',
  `question_ids` text NOT NULL COMMENT '题目列表,逗号分隔',
  `is_finish` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否完成',
  `finish_time` datetime NOT NULL COMMENT '完成时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`),
  KEY `NewIndex1` (`user_id`,`questionnaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='问卷-随机题库';

/*Data for the table `iquestionnaire_random` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
