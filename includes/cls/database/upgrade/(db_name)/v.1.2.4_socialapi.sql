CREATE TABLE IF NOT EXISTS `socialapi` (
`uniqueid` varchar(200) NOT NULL,
`user_id` int(10) unsigned NOT NULL,
`type` ENUM('telegram','facebook', 'twitter') NOT NULL,
`request` text NULL,
`response` text NULL,
`createdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
`datemodified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
UNIQUE KEY `unique` (`uniqueid`,`type`) USING BTREE,
CONSTRAINT `socialapi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;