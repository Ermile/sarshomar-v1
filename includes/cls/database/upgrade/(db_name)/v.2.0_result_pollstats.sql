/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : tejarak

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-10-08 14:52:05
*/

-- ----------------------------
-- Table structure for pollstats
-- ----------------------------
CREATE TABLE IF NOT EXISTS `pollstats` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`post_id` bigint(20) unsigned NOT NULL,
`port` ENUM('site','telegram','sms','api') NOT NULL DEFAULT 'site',
`subport` varchar(64) NULL,
`total` int(10) unsigned,
`result` text,
`gender` text,
`marrital` text,
`graduation` text,
`degree` text,
`employmentstatus` text,
`housestatus` text,
`internetusage` text,
`range` text,
`age` text,
`country` text,
`province` text,
`city` text,
`language` text,
`religion` text,
`course` text,
`industry` text,
`meta` mediumtext,
PRIMARY KEY (`id`),
UNIQUE KEY `unique_post` (`post_id`,`port`,`subport`) USING BTREE,
CONSTRAINT `pollstats_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
