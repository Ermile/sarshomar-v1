ALTER TABLE `apilogs` ADD  `request_header` text NULL AFTER `header`;
ALTER TABLE `apilogs` CHANGE `header` `response_header` text NULL;