-- multi_query
CREATE TRIGGER `terms_change_terms_url_on_update` BEFORE UPDATE ON `terms` FOR EACH ROW
BEGIN
IF(NEW.term_parent != OLD.term_parent) THEN
	SET NEW.term_url = CONCAT_WS('/', (SELECT term_url FROM terms WHERE id = NEW.term_parent LIMIT 1), NEW.term_slug);
END IF;
END