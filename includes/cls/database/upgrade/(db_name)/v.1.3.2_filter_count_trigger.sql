-- multi_query
CREATE TRIGGER `update_filters_count` AFTER UPDATE ON `users` FOR EACH ROW
BEGIN
	UPDATE filters SET `filters`.`usercount` =  `filters`.`usercount` + 1 WHERE filters.id = NEW.filter_id;
	UPDATE filters SET `filters`.`usercount` =  `filters`.`usercount` - 1 WHERE filters.id = OLD.filter_id;
END