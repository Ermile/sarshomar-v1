CREATE TABLE IF NOT EXISTS `userranks` (
`id` 			int(10) 	unsigned NOT NULL AUTO_INCREMENT,
`user_id` 		int(10) 	unsigned NOT NULL,
`reported` 		int(10) 	unsigned NOT NULL DEFAULT 0,
`usespamword` 	int(10) 	unsigned NOT NULL DEFAULT 0,
`changeprofile`	int(10) 	unsigned NOT NULL DEFAULT 0,
`wrongreport`	int(10) 	unsigned NOT NULL DEFAULT 0,
`skip`			int(10) 	unsigned NOT NULL DEFAULT 0,
`resetpassword`	int(10) 	unsigned NOT NULL DEFAULT 0,
`verification`	int(10) 	unsigned NOT NULL DEFAULT 0,
`validation`	int(10) 	unsigned NOT NULL DEFAULT 0,
`other` 		int(10) 	unsigned NOT NULL DEFAULT 0,
`createdate`timestamp 				 NOT NULL DEFAULT CURRENT_TIMESTAMP,
`value` 	bigint(20) 			 	 NOT NULL DEFAULT 0,
PRIMARY KEY (`id`),
UNIQUE KEY `unique_user_id` (`user_id`) USING BTREE,
CONSTRAINT `ranks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;