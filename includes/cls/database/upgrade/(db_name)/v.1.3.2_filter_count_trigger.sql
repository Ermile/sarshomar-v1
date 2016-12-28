-- multi_query
CREATE TRIGGER `update_filters_count` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
IF(NEW.filter_id != OLD.filter_id) THEN
	IF(NEW.filter_id IS NOT NULL) THEN
		UPDATE filters	SET `usercount` =  IF(`usercount` IS NULL , 1, `usercount` + 1) WHERE filters.id = NEW.filter_id;
		IF(OLD.filter_id IS NOT NULL) THEN
			UPDATE filters SET `usercount` =  IF(`usercount` IS NULL OR `usercount` = 0, 0, `usercount` - 1) WHERE filters.id = OLD.filter_id;
		END IF;
	END IF;
END IF;
END