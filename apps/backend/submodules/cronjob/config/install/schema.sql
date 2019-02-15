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

/*Table structure for table `icronjob_job` */

DROP TABLE IF EXISTS `icronjob_job`;

CREATE TABLE `icronjob_job` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '计划任务ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '计划任务名称',
  `desc` varchar(100) NOT NULL DEFAULT '' COMMENT '计划任务功能描述',
  `start_time` datetime NOT NULL COMMENT '执行开始时间',
  `end_time` datetime NOT NULL COMMENT '任务结束时间',
  `cmd` varchar(100) NOT NULL DEFAULT '' COMMENT '任务命令',
  `cycle` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '执行周期(分钟)',
  `cron` char(20) NOT NULL DEFAULT '' COMMENT 'cron语法',
  `last_execute_time` datetime NOT NULL COMMENT '最后一次执行时间',
  `last_execute_result` text NOT NULL COMMENT '最后一次执行结果',
  `script_execute_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '脚本执行时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='计划任务表';

/*Data for the table `icronjob_job` */

insert  into `icronjob_job`(`_id`,`name`,`desc`,`start_time`,`end_time`,`cmd`,`cycle`,`cron`,`last_execute_time`,`last_execute_result`,`script_execute_time`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('58f6fe14befcf2ed008b4568','测试1','测试1','2017-04-19 00:00:00','2027-04-19 23:59:59','main test a b',0,'*/2 * * * *','2017-04-21 14:26:00','hello a\nbest regards, b\n',10,'2017-04-19 14:04:48','2017-04-21 14:26:38',0);

/*Table structure for table `icronjob_log` */

DROP TABLE IF EXISTS `icronjob_log`;

CREATE TABLE `icronjob_log` (
  `_id` char(24) NOT NULL DEFAULT '' COMMENT '计划任务日志ID',
  `job_name` varchar(30) NOT NULL DEFAULT '' COMMENT '计划任务',
  `execute_result` text NOT NULL COMMENT '执行结果',
  `script_execute_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '脚本执行时间',
  `__CREATE_TIME__` datetime NOT NULL COMMENT '创建时间',
  `__MODIFY_TIME__` datetime NOT NULL COMMENT '修改时间',
  `__REMOVED__` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC COMMENT='计划任务执行日志表';

/*Data for the table `icronjob_log` */

insert  into `icronjob_log`(`_id`,`job_name`,`execute_result`,`script_execute_time`,`__CREATE_TIME__`,`__MODIFY_TIME__`,`__REMOVED__`) values ('58f70270befcf28b018b4567','58f6fe14befcf2ed008b4568','hello a\nbest regards, b\n',1,'2017-04-19 14:23:44','2017-04-19 14:23:44',0),('58f9a61d423394f7008b4567','58f6fe14befcf2ed008b4568','hello a\nbest regards, b\n',10,'2017-04-21 14:26:37','2017-04-21 14:26:37',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
