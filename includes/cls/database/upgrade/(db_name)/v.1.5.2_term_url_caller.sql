-- multi_query
CREATE TRIGGER `terms_set_terms_url_caller_on_insert` BEFORE INSERT ON `terms` FOR EACH ROW
BEGIN
IF(NEW.term_parent IS NOT NULL AND NEW.term_parent != '') THEN
	SET NEW.term_url = CONCAT_WS('/', (SELECT term_url FROM terms WHERE id = NEW.term_parent LIMIT 1), NEW.term_slug);
	SET NEW.term_caller = CONCAT_WS(':', (SELECT term_caller FROM terms WHERE id = NEW.term_parent LIMIT 1), NEW.term_slug);
END IF;
IF(NEW.term_parent IS NULL OR NEW.term_parent = '') THEN
	SET NEW.term_url    = CONCAT_WS('/', '$', NEW.term_slug);
	SET NEW.term_caller = NEW.term_slug;
END IF;
END