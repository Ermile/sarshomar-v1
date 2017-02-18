ALTER TABLE `apilogs` ADD `apikeyuserid` int(10) unsigned NULL AFTER `api_key`;
ALTER TABLE `apilogs` CHANGE `api_key` `apikey` varchar(200) NULL;
ALTER TABLE `apilogs` CHANGE `page_status` `pagestatus` varchar(50) NULL;
ALTER TABLE `apilogs` ADD `clientip` varchar(50) NULL AFTER `desc`;
ALTER TABLE `apilogs` ADD `visit_id` bigint(20) unsigned NULL AFTER `desc`;

ALTER TABLE `apilogs` CHANGE `request_header` `requestheader` text NULL;
ALTER TABLE `apilogs` CHANGE `response_header` `responseheader` text NULL;
