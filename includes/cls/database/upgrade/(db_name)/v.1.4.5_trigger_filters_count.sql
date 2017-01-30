-- multi_query
CREATE TRIGGER `users_change_filters_count_on_update` AFTER UPDATE ON `users` FOR EACH ROW
BEGIN
	-- plus new filter count
	UPDATE IGNORE filters SET filters.count = IF(filters.count IS NULL OR filters.count = '', 1, filters.count + 1) WHERE filters.id = NEW.filter_id LIMIT 1;
	-- minus old filter count
	UPDATE IGNORE filters SET filters.count = IF(filters.count IS NULL OR filters.count = '' OR filters.count < 1, 0, filters.count - 1) WHERE filters.id = OLD.filter_id LIMIT 1;
END