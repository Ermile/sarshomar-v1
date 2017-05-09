ALTER TABLE `election`.`elections` ADD `en_title`	varchar(500) NOT NULL;

ALTER TABLE `election`.`candidas` ADD `en_url`		varchar(500) NULL DEFAULT NULL;
ALTER TABLE `election`.`candidas` ADD `en_name`		varchar(255) NOT NULL;
ALTER TABLE `election`.`candidas` ADD `en_family`	varchar(255) NOT NULL;
ALTER TABLE `election`.`candidas` ADD `en_father`	varchar(255) NULL;
ALTER TABLE `election`.`candidas` ADD `en_fame`		varchar(255) NULL;