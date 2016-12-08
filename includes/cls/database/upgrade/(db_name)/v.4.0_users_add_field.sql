ALTER TABLE `users` ADD `filter_id` bigint(20) UNSIGNED NULL;
ALTER TABLE `users` ADD CONSTRAINT `users_filters_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `filters` (`id`) ON UPDATE CASCADE;

ALTER TABLE `posts` ADD `filter_id` bigint(20) UNSIGNED NULL;
ALTER TABLE `posts` ADD CONSTRAINT `posts_filters_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `filters` (`id`) ON UPDATE CASCADE;
ALTER TABLE `posts` ADD `post_survey` bigint(20) UNSIGNED NULL;
ALTER TABLE `posts` ADD `post_gender` ENUM('poll', 'survey') NULL;
ALTER TABLE `posts` ADD `post_sarshomar` bit(1) NULL DEFAULT NULL;
ALTER TABLE `posts` ADD `post_privacy` ENUM('public', 'private') NULL;
ALTER TABLE `posts` ADD `post_rank` int(10) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `polldetails` CHANGE `type` `type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `posts`  CHANGE `post_status` `post_status` ENUM('stop','pause','trash','publish','draft','enable','disable','deleted','schedule','expired','awaiting','filtered','blocked','spam','violence','pornography','other') NOT NULL DEFAULT 'draft';
ALTER TABLE `posts` ADD `comment_count` int(20) UNSIGNED NULL;


ALTER TABLE `pollstats` DROP FOREIGN KEY 'pollstats_ibfk_1';
ALTER TABLE `pollstats` ADD `type` ENUM('valid', 'invalid') NOT NULL AFTER `subport`;
ALTER TABLE `pollstats` DROP INDEX 'unique_post';
ALTER TABLE `pollstats` ADD CONSTRAINT 'unique_post' UNIQUE (`post_id`,`port`,`subport`, `type`) USING BTREE;
ALTER TABLE `pollstats` ADD CONSTRAINT `pollstats_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON UPDATE CASCADE;

ALTER TABLE `polldetails` ADD `validstatus` ENUM('valid','invalid') NOT NULL AFTER `opt`;
