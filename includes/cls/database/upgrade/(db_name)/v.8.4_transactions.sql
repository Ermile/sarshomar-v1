CREATE TABLE IF NOT EXISTS `transactions` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(500) NOT NULL,
`transactionitem_id` int(10) unsigned NOT NULL,
`user_id` smallint(5) unsigned NOT NULL,
`type` ENUM('real','gift','prize','transfer') NOT NULL,
`unit_id` int(10) unsigned NOT NULL,
`plus` float(53) NULL,
`minus` float(53) NULL,
`budgetbefor` float(53) NULL,
`budget` float(53) NULL,
`exchange_id` bigint(20) unsigned NULL,
`status` ENUM('enable','disable','deleted','expired','awaiting','filtered','blocked','spam') NOT NULL,
`meta` text NULL,
`desc` text NULL,
`related_user_id` int(10) unsigned NULL,
`parent_id` bigint(20) unsigned NULL,
`finished` ENUM('yes', 'no') NOT NULL DEFAULT 'no',
`createdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
`datemodified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) 			REFERENCES `users` (`id`) 			 ON UPDATE CASCADE,
CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`related_user_id`) 	REFERENCES `users` (`id`) 			 ON UPDATE CASCADE,
CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`parent_id`) 			REFERENCES `transactions` (`id`) 	 ON UPDATE CASCADE,
CONSTRAINT `transactions_ibfk_4` FOREIGN KEY (`exchange_id`) 		REFERENCES `exchanges` (`id`) 		 ON UPDATE CASCADE,
CONSTRAINT `transactions_ibfk_5` FOREIGN KEY (`transactionitem_id`) REFERENCES `transactionitems` (`id`) ON UPDATE CASCADE,
CONSTRAINT `transactions_ibfk_6` FOREIGN KEY (`unit_id`) 			REFERENCES `units` (`id`) 			 ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
