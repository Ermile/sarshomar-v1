ALTER TABLE `users` ADD `user_parent` INT(10) UNSIGNED NULL AFTER `user_createdate`;
ALTER TABLE `users` ADD `user_validstatus` ENUM('valid', 'invalid') NOT NULL DEFAULT 'invalid' AFTER `user_parent`;
UPDATE users SET user_validstatus = 'valid' WHERE user_status = 'active';