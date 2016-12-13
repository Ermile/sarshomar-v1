CREATE TABLE IF NOT EXISTS `units` (
`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(500) NOT NULL,
`desc` text NULL,
`meta` text NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `unique_title` (`title`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;