ALTER TABLE `users` ADD `filter_id` bigint(20) UNSIGNED NULL;
ALTER TABLE `users` ADD CONSTRAINT `users_filters_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `filters` (`id`) ON UPDATE CASCADE;

ALTER TABLE `posts` ADD `filter_id` bigint(20) UNSIGNED NULL;
ALTER TABLE `posts` ADD CONSTRAINT `posts_filters_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `filters` (`id`) ON UPDATE CASCADE;
ALTER TABLE `posts` ADD `post_survey` bigint(20) UNSIGNED NULL;
ALTER TABLE `posts` ADD `post_gender` ENUM('poll', 'survey') NULL;
