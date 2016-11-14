ALTER TABLE `users` ADD `filter_id` bigint(20) UNSIGNED NULL;
ALTER TABLE `users` ADD CONSTRAINT `users_filters_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `filters` (`id`) ON UPDATE CASCADE;

ALTER TABLE `posts` ADD `filter_id` bigint(20) UNSIGNED NULL;
ALTER TABLE `posts` ADD CONSTRAINT `posts_filters_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `filters` (`id`) ON UPDATE CASCADE;
ALTER TABLE `posts` ADD `post_survey` bigint(20) UNSIGNED NULL;
ALTER TABLE `posts` ADD `post_gender` ENUM('poll', 'survey') NULL;
ALTER TABLE `posts` ADD `post_sarshomar` bit(1) NULL DEFAULT NULL;
ALTER TABLE `posts` ADD `post_privacy` ENUM('public', 'private') NULL;

ALTER TABLE `polldetails` CHANGE `type` `type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `posts`  CHANGE `post_status` `post_status` ENUM('stop','pause','trash','publish','draft','enable','disable','deleted','schedule','expired','awaiting','filtered','blocked','spam','violence','pornography','other') NOT NULL DEFAULT 'draft';