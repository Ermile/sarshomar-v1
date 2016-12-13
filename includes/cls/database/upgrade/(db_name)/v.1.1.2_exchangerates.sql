CREATE TABLE IF NOT EXISTS `exchangerates` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`from` int(10) unsigned NOT NULL,
`to` int(10) unsigned NOT NULL,
`rate` float(53) NOT NULL,
`roundtype` ENUM('up','down','round') NULL,
`round` float(53) NULL,
`wagestatic` float(53) NULL,
`wage` float(53) NULL,
`status` ENUM('enable','disable','deleted','expired','awaiting','filtered','blocked','spam') NOT NULL,
`desc` text NULL,
`meta` text NULL,
`createdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
`datemodified` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
`enddate` TIMESTAMP NULL,
PRIMARY KEY (`id`),
CONSTRAINT `exchangerates_ifbk_1` FOREIGN KEY (`from`) REFERENCES `units` (`id`) ON UPDATE CASCADE,
CONSTRAINT `exchangerates_ifbk_2` FOREIGN KEY (`to`) REFERENCES `units` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
