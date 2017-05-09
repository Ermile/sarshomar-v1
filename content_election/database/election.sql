CREATE DATABASE `election` DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE election.elections (
`id`			int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`title`			varchar(500) NOT NULL,
`en_title`			varchar(500) NOT NULL,
`status`		enum('awaiting','running','done') DEFAULT 'awaiting',
`eligible`		int(10) UNSIGNED NULL DEFAULT NULL,
`voted`			int(10) UNSIGNED NULL DEFAULT NULL,
`invalid`		int(10) UNSIGNED NULL DEFAULT NULL,
`cash`			int(10) UNSIGNED NULL DEFAULT NULL,
`branchs`		int(10) UNSIGNED NULL DEFAULT NULL,
`first_vote_male_count`int(10) UNSIGNED NULL DEFAULT NULL,
`first_vote_female_count`int(10) UNSIGNED NULL DEFAULT NULL,
`signuped_count`int(10) UNSIGNED NULL DEFAULT NULL,
`verified_count`int(10) UNSIGNED NULL DEFAULT NULL,
`candida_count`	int(10) UNSIGNED NULL DEFAULT NULL,
`start_time`	datetime DEFAULT NULL,
`end_time`		datetime DEFAULT NULL,
`election_date`	datetime DEFAULT NULL,
`jalali_year`	int(4) DEFAULT NULL,
`year` 			int(4) DEFAULT NULL,
`en_url`		varchar(500) NULL DEFAULT NULL,
`fa_url`		varchar(500) NULL DEFAULT NULL,
`cat`			varchar(255) NULL DEFAULT NULL,
`win`			int(10) UNSIGNED NULL DEFAULT NULL,
`createdate`	datetime DEFAULT CURRENT_TIMESTAMP,
`date_modified`	timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`desc`			text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
`meta`			mediumtext 	CHARACTER SET utf8mb4 NULL DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE election.candidas (
`id`			int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`election_id`	int(10) UNSIGNED NOT NULL,
`name`			varchar(255) NOT NULL,
`en_name`			varchar(255) NOT NULL,
`family`		varchar(255) NOT NULL,
`en_family`		varchar(255) NOT NULL,
`father`		varchar(255) NULL,
`en_father`		varchar(255) NULL,
`fame`			varchar(255) NULL,
`en_fame`			varchar(255) NULL,
`nationalcode`	varchar(255) NULL,
`birthdate`		datetime NULL,
`electioncode`	varchar(255) NULL,
`file_url`		varchar(255) NULL,
`file_url_2`	varchar(255) NULL,
`win_url`		varchar(255) NULL,
`status`		enum('active','cancel') NOT NULL DEFAULT 'active',
`createdate`	datetime DEFAULT CURRENT_TIMESTAMP,
`date_modified`	timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`desc`			text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
`meta`				mediumtext 		CHARACTER SET utf8mb4 NULL DEFAULT NULL,
PRIMARY KEY (`id`),
CONSTRAINT `candida_election_id` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE election.reports (
`id` 				int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`election_id` 		int(10) UNSIGNED NOT NULL,
`date` 				datetime NULL DEFAULT NULL,
`level` 			varchar(255) NULL DEFAULT NULL,
`number` 			smallint(3) NULL DEFAULT NULL,
`cash` 				int(10) UNSIGNED NULL DEFAULT NULL,
`voted` 			int(10) UNSIGNED NULL DEFAULT NULL,
`invalid` 			int(10) UNSIGNED NULL DEFAULT NULL,
`status` 			enum('enable', 'disable') DEFAULT 'enable',
`createdate` 		datetime DEFAULT CURRENT_TIMESTAMP,
`date_modified` 	timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`desc` 				text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
`meta` 					mediumtext 		CHARACTER SET utf8mb4 NULL DEFAULT NULL,
PRIMARY KEY (`id`),
CONSTRAINT `reports_election_id` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE election.results (
`id` 			int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
`election_id` 	int(10) UNSIGNED NOT NULL,
`report_id` 	int(10) UNSIGNED NULL DEFAULT NULL,
`candida_id` 	int(10) UNSIGNED NOT NULL,
`total` 		bigint(20) UNSIGNED NOT NULL,
`status` 		enum('enable', 'disable') DEFAULT 'enable',
`createdate` 	datetime DEFAULT CURRENT_TIMESTAMP,
`date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`desc` 			text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
`meta` 				mediumtext 		CHARACTER SET utf8mb4 NULL DEFAULT NULL,
PRIMARY KEY (`id`),
CONSTRAINT `results_election_id` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON UPDATE CASCADE,
CONSTRAINT `results_report_id` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE election.resultbyplaces (
`id` 			bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
`election_id` 	int(10) UNSIGNED NOT NULL,
`report_id` 	int(10) UNSIGNED NULL DEFAULT NULL,
`candida_id` 	int(10) UNSIGNED NOT NULL,
`location_type`	enum('country', 'province', 'city') NOT NULL,
`place` 		int(10) UNSIGNED NOT NULL,
`total` 		bigint(20) UNSIGNED NULL DEFAULT NULL,
`status` 		enum('enable', 'disable') DEFAULT 'enable',
`createdate` 	datetime DEFAULT CURRENT_TIMESTAMP,
`date_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
`desc` 			text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
`meta` 			mediumtext 		CHARACTER SET utf8mb4 NULL DEFAULT NULL,
PRIMARY KEY (`id`),
CONSTRAINT `resultbyplase_election_id` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON UPDATE CASCADE,
CONSTRAINT `resultbyplase_candida_id` FOREIGN KEY (`candida_id`) REFERENCES `candidas` (`id`) ON UPDATE CASCADE,
CONSTRAINT `resultbyplase_report_id` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
