CREATE TABLE IF NOT EXISTS `polltrees` (
`id` bigint(20)	unsigned NOT NULL AUTO_INCREMENT,
`post_id` bigint(20) unsigned NOT NULL,
`parent` bigint(20) unsigned NOT NULL,
`opt` smallint(3) unsigned NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `polltrees_unique` (`post_id`,`parent`, `opt`) USING BTREE,
CONSTRAINT `polltrees_post_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE,
CONSTRAINT `polltrees_parent` FOREIGN KEY (`parent`) REFERENCES `posts` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;