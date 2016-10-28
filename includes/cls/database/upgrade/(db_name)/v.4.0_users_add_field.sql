ALTER TABLE `users` ADD `filter_id` INT(10) UNSIGNED NULL;
-- ALTER TABLE `users` ADD CONSTRAINT `users_filters_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `filters` (`id`);
ALTER TABLE `posts` ADD `filter_id` INT(10) UNSIGNED NULL;
-- ALTER TABLE `posts` ADD CONSTRAINT `posts_filters_ibfk_1` FOREIGN KEY (`filter_id`) REFERENCES `filters` (`id`);
ALTER TABLE `posts` ADD `survey` tinyint(1) NULL;

ALTER TABLE `users` ADD `pollanswer`		INT(10) 			NULL;
ALTER TABLE `users` ADD `pollskipped`		INT(10) 			NULL;
ALTER TABLE `users` ADD `point` 			INT(10) 			NULL;
ALTER TABLE `users` ADD `surveycount`		INT(10) 			NULL;
ALTER TABLE `users` ADD `pollcount`			INT(10) 			NULL;
ALTER TABLE `users` ADD `peopleanswer`		INT(10) 			NULL;
ALTER TABLE `users` ADD `peopleskipped`		INT(10) 			NULL;
ALTER TABLE `users` ADD `userreferred`		INT(10) 			NULL;
ALTER TABLE `users` ADD `userverified`		INT(10) 			NULL;
