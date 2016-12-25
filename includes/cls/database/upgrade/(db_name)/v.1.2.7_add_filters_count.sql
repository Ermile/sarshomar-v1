ALTER TABLE `filters` DROP  `unique`;
ALTER TABLE `filters` ADD  `count` INT (10) unsigned NOT NULL DEFAULT 0 AFTER `id`;