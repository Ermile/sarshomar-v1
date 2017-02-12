CREATE TABLE IF NOT EXISTS `apilogs` (
`id` 			bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`user_id` 		int(10) unsigned NULL,
`url` 			varchar(760) NULL,
`method` 		varchar(50) NULL,
`header` 		text NULL,
`request` 		text NULL,
`response` 		text NULL,
`page_status` 	varchar(50) NULL,
`status` 		varchar(255) NULL,
`debug` 		text NULL,
`api_key` 		varchar(200) NULL,
`token` 		varchar(200) NULL,
`meta` 			text NULL,
`desc` 			text NULL,
`createdate` 	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
`datemodified` 	TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;