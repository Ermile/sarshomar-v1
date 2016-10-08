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
CREATE TABLE `pollstats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(10) unsigned NOT NULL,
  `total` text,
  `gender` text,
  `marrital` text,
  `parental` text,
  `exercise` text,
  `employment` text,
  `business` text,
  `industry` text,
  `devices` text,
  `internet_usage` text,
  `birthdate` text,
  `age` text,
  `range` text,
  `graduation` text,
  `course` text,
  `countrybirth` text,
  `country` text,
  `provincebirth` text,
  `province` text,
  `birthcity` text,
  `city` text,
  `citybirth` text,
  `language` text,
  `meta` mediumtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_post` (`post_id`) USING BTREE,
  CONSTRAINT `pollstats_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
