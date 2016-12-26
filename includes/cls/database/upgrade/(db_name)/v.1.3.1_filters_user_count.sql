ALTER TABLE `filters` DROP  `count`;
ALTER TABLE `filters` ADD  `usercount` INT (10) unsigned NOT NULL DEFAULT 0 AFTER `id`;