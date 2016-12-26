-- multi_query
CREATE TRIGGER `update_filters_count` AFTER UPDATE ON `users` FOR EACH ROW
BEGIN
	IF(NEW.filter_id IS NOT NULL) THEN
		BEGIN
			UPDATE filters SET filters.count = IF(filters.count IS NULL, 1, filters.count + 1) WHERE filters.id = NEW.filter_id;
		END;
	END IF;

	IF(OLD.filter_id IS NOT NULL) THEN
		BEGIN
			UPDATE filters SET filters.count = IF(filters.count > 0, filters.count - 1, 0) WHERE filters.id = OLD.filter_id;
		END;
	END IF;
END