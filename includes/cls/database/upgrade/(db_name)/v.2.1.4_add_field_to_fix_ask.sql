ALTER TABLE `users` ADD `user_ask` BIGINT(20) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `posts` ADD `post_member` INT(10) UNSIGNED NULL DEFAULT 0;
ALTER TABLE `posts` ADD `post_asked` INT(10) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE `posts` ADD `post_hasfilter` BIT(1) NULL DEFAULT 0;
ALTER TABLE `posts` ADD `post_hasmedia` BIT(1) NULL DEFAULT 0;
ALTER TABLE `posts` ADD `post_prize` INT(10) UNSIGNED  NULL DEFAULT NULL;
ALTER TABLE `posts` ADD `post_prizeunit` SMALLINT(5) UNSIGNED  NULL DEFAULT NULL;
ALTER TABLE `posts` ADD CONSTRAINT `posts_post_prizeunit` FOREIGN KEY (`post_prizeunit`) REFERENCES `units` (`id`) ON UPDATE CASCADE;
ALTER TABLE `posts` ADD `post_password` VARCHAR(50)  NULL DEFAULT NULL;
ALTER TABLE `posts` ADD `post_brand` VARCHAR(255)  NULL DEFAULT NULL;
ALTER TABLE `posts` ADD `post_brandurl` VARCHAR(255)  NULL DEFAULT NULL;

UPDATE users SET users.user_ask =
(
	SELECT options.option_value
	FROM options
	WHERE options.option_cat = CONCAT('user_detail_', users.id)
	AND options.option_key = 'user_ask_me'
	AND options.user_id = users.id
	LIMIT 1
);

UPDATE posts SET posts.post_hasmedia =
(
	SELECT IF(options.option_value IS NULL, 0, 1)
	FROM options
	WHERE options.option_cat  = CONCAT('poll_', posts.id)
	AND options.option_key    = 'title_attachment'
	AND options.post_id       = posts.id
	AND options.option_status = 'enable'
	LIMIT 1
);

UPDATE posts SET posts.post_prize =
(
	SELECT options.option_value
	FROM options
	WHERE options.option_cat  = CONCAT('poll_', posts.id)
	AND options.option_key    = 'prize'
	AND options.post_id       = posts.id
	AND options.option_status = 'enable'
	LIMIT 1
);


UPDATE posts SET posts.post_prizeunit =
(
	SELECT (SELECT units.id FROM units WHERE units.title = TRIM(BOTH '"' FROM JSON_EXTRACT(options.option_meta, '$.unit')) LIMIT 1)
	FROM options
	WHERE options.option_cat  = CONCAT('poll_', posts.id)
	AND options.option_key    = 'prize'
	AND options.post_id       = posts.id
	AND options.option_status = 'enable'
	LIMIT 1
);

UPDATE posts SET posts.post_brand =
(
	SELECT option_value
	FROM options
	WHERE options.option_cat  = CONCAT('poll_', posts.id)
	AND options.option_key    = 'brand'
	AND options.post_id       = posts.id
	AND options.option_status = 'enable'
	LIMIT 1
);
UPDATE posts SET posts.post_brandurl =
(
	SELECT TRIM(BOTH '"' FROM JSON_EXTRACT(options.option_meta, '$.url'))
	FROM options
	WHERE options.option_cat  = CONCAT('poll_', posts.id)
	AND options.option_key    = 'brand'
	AND options.post_id       = posts.id
	AND options.option_status = 'enable'
	LIMIT 1
);

UPDATE posts SET post_member =
(
	SELECT IFNULL(ranks.member,0)
	FROM ranks
	WHERE ranks.post_id = posts.id
);

-- UPDATE posts SET post_hasfilter = 1 WHERE posts.id IN
-- (
-- 	SELECT DISTINCT termusages.termusage_id
-- 	FROM `termusages`
-- 	WHERE `termusage_foreign` = 'filter'
-- 	AND `termusage_status` = 'enable'
-- );

UPDATE posts SET posts.post_password =
(
	SELECT options.option_value
	FROM options
	WHERE options.option_cat  = CONCAT('poll_', posts.id)
	AND options.option_key    = 'password'
	AND options.post_id       = posts.id
	AND options.option_status = 'enable'
	LIMIT 1
);


