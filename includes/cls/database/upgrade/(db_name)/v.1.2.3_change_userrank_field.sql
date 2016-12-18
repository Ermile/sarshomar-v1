ALTER TABLE `userranks` CHANGE `goodreport` `report` int(10) unsigned NOT NULL DEFAULT 0;

ALTER TABLE `userranks` ADD `pollanswered` 		int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `userranks` ADD `pollskipped` 		int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `userranks` ADD `surveyanswered` 	int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `userranks` ADD `surveyskipped` 	int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `userranks` ADD `mypollanswered` 	int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `userranks` ADD `mypollskipped` 	int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `userranks` ADD `mysurveyanswered` 	int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `userranks` ADD `mysurveyskipped` 	int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `userranks` ADD `userreferred` 		int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `userranks` ADD `userverified` 		int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `userranks` ADD `commentcount` 		int(10) unsigned NOT NULL DEFAULT 0;