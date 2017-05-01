CREATE TABLE IF NOT EXISTS `answers` (
`id` bigint(20)	unsigned NOT NULL AUTO_INCREMENT,
`post_id` bigint(20) unsigned NOT NULL,
`user_id` int(10) unsigned NOT NULL,
`lastopt` tinyint(3) NULL DEFAULT NULL,
`ask` bit(1) NULL DEFAULT NULL,
`countupdate` int(10) unsigned NULL DEFAULT NULL,
`createdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
`date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
UNIQUE KEY `answers_unique` (`post_id`,`user_id`) USING BTREE,
CONSTRAINT `answers_post_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE,
CONSTRAINT `answers_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;