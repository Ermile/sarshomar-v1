/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : tejarak

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2016-10-08 14:52:26
*/

-- ----------------------------
-- Table structure for polldetails
-- ----------------------------
CREATE TABLE IF NOT EXISTS `polldetails` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`post_id` bigint(10) unsigned NOT NULL,
`user_id` int(10) unsigned NOT NULL,
`port` ENUM('site','telegram','sms','api') NOT NULL DEFAULT 'site',
`subport` bigint(20) unsigned NULL,
`opt` tinyint(3) unsigned DEFAULT NULL,
`type` varchar(50) NULL DEFAULT NULL,
`txt` text,
`profile` bigint(20) unsigned,
`insertdate` datetime NULL,
`visitor_id` bigint(20) unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `unique_opt` (`post_id`,`user_id`,`opt`) USING BTREE,
KEY `polldetails_ibfk_2` (`user_id`),
CONSTRAINT `polldetails_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE,
CONSTRAINT `polldetails_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
