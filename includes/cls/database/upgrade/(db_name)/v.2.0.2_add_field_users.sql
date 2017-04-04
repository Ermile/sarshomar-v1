ALTER TABLE `users` ADD `user_language` char(2) NULL DEFAULT NULL;
ALTER TABLE `users` ADD `unit_id` smallint(5) unsigned NULL DEFAULT NULL;
ALTER TABLE `users` ADD CONSTRAINT `users_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON UPDATE CASCADE;
UPDATE IGNORE users SET user_language =
(
	SELECT options.option_value
	FROM options
	WHERE
	options.post_id IS NULL AND
	options.user_id    = users.id AND
	options.option_cat = CONCAT('user_detail_', users.id) AND
	options.option_key = 'language'
	LIMIT 1
)
