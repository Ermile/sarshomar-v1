CREATE TABLE `answerdetails` (
`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
`answer_id` bigint(20) UNSIGNED NOT NULL,
`post_id` bigint(10) UNSIGNED NOT NULL,
`user_id` int(10) UNSIGNED NOT NULL,
`port` enum('site','telegram','sms','api') NOT NULL DEFAULT 'site',
`validstatus` enum('valid','invalid') DEFAULT NULL,
`subport` varchar(100) DEFAULT NULL,
`opt` tinyint(3) UNSIGNED DEFAULT NULL,
`answertype` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
`type` varchar(50) DEFAULT NULL,
`txt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
`profile` bigint(20) UNSIGNED DEFAULT NULL,
`ask` bit(1) NULL DEFAULT NULL,
`visitor_id` bigint(20) UNSIGNED DEFAULT NULL,
`status` enum('enable','disable','deleted') DEFAULT 'enable',
`createdate` datetime DEFAULT CURRENT_TIMESTAMP,
`date_affected` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
`date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
CONSTRAINT `answersdetail_answer_id` FOREIGN KEY (`answer_id`) REFERENCES `answers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
