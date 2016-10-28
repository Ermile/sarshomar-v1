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
-- Table structure for filters
-- ----------------------------
CREATE TABLE IF NOT EXISTS `filters` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`gender` ENUM('male','female') NULL,
`marrital` ENUM('single', 'marriade') NULL,
`birthday` DATE NULL,
`age` SMALLINT(3) NULL,
`language` VARCHAR(2) NULL,
`graduation` VARCHAR(64) NULL,
`course` VARCHAR(150)NULL,
`employment` VARCHAR(64) NULL,
`business` VARCHAR(150)NULL,
`industry`VARCHAR(150)NULL,
`countrybirth` VARCHAR(64) NULL,
`provincebirth` VARCHAR(64)NULL,
`citybirth` VARCHAR(64) NULL,
`country` VARCHAR(64) NULL,
`province` VARCHAR(64) NULL,
`city` VARCHAR(64) NULL,
`parental` VARCHAR(64) NULL,
`exercise` VARCHAR(64) NULL,
`devices` VARCHAR(64) NULL,
`internetusage` ENUM('low', 'mid', 'high')NULL,
`unique` VARCHAR(760) NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `unique_filters` (`unique`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
