CREATE TABLE IF NOT EXISTS `exchanges` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`exchangerate_id` bigint(20) unsigned NOT NULL,
`valuefrom` float(53) NOT NULL,
`valueto` float(53) NOT NULL,
`meta` text NULL,
`desc` text NULL,
PRIMARY KEY (`id`),
CONSTRAINT `exchanges_ibfk_1` FOREIGN KEY (`exchangerate_id`) REFERENCES `exchangerates` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
