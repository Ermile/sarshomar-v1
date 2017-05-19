CREATE TABLE IF NOT EXISTS election.comments (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned DEFAULT NULL,
  `author` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `content` mediumtext NOT NULL,
  `meta` mediumtext,
  `status` enum('approved','unapproved','spam','deleted') NOT NULL DEFAULT 'unapproved',
  `parent` smallint(5) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `visitor_id` bigint(20) unsigned DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
