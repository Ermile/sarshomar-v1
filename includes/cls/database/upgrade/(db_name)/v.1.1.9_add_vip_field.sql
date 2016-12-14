ALTER TABLE `ranks` ADD `vip` int(10)  unsigned NOT NULL DEFAULT 0 AFTER `ago`;
ALTER TABLE `ranks` ADD `public` bit(1)  NOT NULL DEFAULT 0 AFTER `member`;
ALTER TABLE `ranks` ADD `admin` int(10)  unsigned NOT NULL DEFAULT 0 AFTER `ago`;
ALTER TABLE `ranks` ADD `money` int(10)  unsigned NOT NULL DEFAULT 0 AFTER `filter`;
ALTER TABLE `ranks` ADD `ad` int(10)  unsigned NOT NULL DEFAULT 0 AFTER `filter`;