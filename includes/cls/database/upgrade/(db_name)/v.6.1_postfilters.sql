CREATE TABLE IF NOT EXISTS `postfilters` (
`post_id` bigint(20) unsigned NOT NULL,
`filter_id` bigint(20) unsigned NOT NULL,
CONSTRAINT `postfilters_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE,
CONSTRAINT `postfilters_ibfk_2` FOREIGN KEY (`filter_id`) REFERENCES `filters` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;