ALTER TABLE `pollstats` ADD `port` ENUM('site','telegram','sms','api') NULL AFTER `post_id`;
ALTER TABLE `pollstats` ADD `subport` TEXT NULL AFTER `port`;

ALTER TABLE `polldetails` ADD `port` ENUM('site','telegram','sms','api') NULL AFTER `user_id`;
ALTER TABLE `polldetails` ADD `subport` TEXT NULL AFTER `port`;