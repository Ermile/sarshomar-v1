CREATE TABLE IF NOT EXISTS `transactionitems` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(500) NOT NULL,
`caller` varchar(500) NOT NULL,
`unit_id` int(10) unsigned NOT NULL,
`type` ENUM('real','gift','prize','transfer') NOT NULL,
`minus` float(53) NULL,
`plus` float(53) NULL,
`autoverify` ENUM('yes','no') NOT NULL DEFAULT 'no',
`forcechange` ENUM('yes','no') NOT NULL DEFAULT 'no',
`desc` text NULL,
`meta` text NULL,
`status` ENUM('enable','disable','deleted','expired','awaiting','filtered','blocked','spam') NOT NULL,
`createdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
`datemodified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
`enddate` TIMESTAMP NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `unique_caller` (`caller`) USING BTREE,
CONSTRAINT `transactionitems_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
