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
`marrital` ENUM('single', 'married') NULL,
`internetusage` ENUM('low', 'mid', 'high')NULL,
`graduation` ENUM('illiterate','undergraduate','graduate') NULL,
`degree` ENUM('under diploma','diploma','2 year college','bachelor','master','phd','other') NULL,
`course` VARCHAR(200)NULL,
`age` SMALLINT(3) NULL,
`agemin` SMALLINT(3) NULL,
`agemax` SMALLINT(3) NULL,
`range` ENUM('-13','14-17','18-24','25-30','31-44','45-59','60+') NULL,
`country` VARCHAR(64) NULL,
`province` VARCHAR(64) NULL,
`city` VARCHAR(64) NULL,
`employmentstatus` ENUM('employee','unemployed','retired') NULL,
`housestatus` ENUM('owner','tenant','homeless') NULL,
`religion` VARCHAR(64) NULL,
`language` VARCHAR(2) NULL,
`industry`VARCHAR(200)NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;