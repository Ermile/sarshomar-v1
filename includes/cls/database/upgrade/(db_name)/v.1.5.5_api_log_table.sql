CREATE TABLE IF NOT EXISTS `api_log` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`user_id` int(10) unsigned NOT NULL,
`url` varchar(760) NULL,
`header` text NULL,
`request` text NULL,
`response` text NULL,
`status` text NULL,
`debug` text NULL,
`api_key` varchar(200) NULL,
`token` varchar(200) NULL,
`createdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
`datemodified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;