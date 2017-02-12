-- multi_query
CREATE TRIGGER `termusages_update_options_record_offline_count_on_delete` AFTER DELETE ON `termusages` FOR EACH ROW
BEGIN

UPDATE IGNORE
	options
SET
	options.option_meta = IF(options.option_meta IS NULL OR options.option_meta = '' OR options.option_meta < 1, 0, options.option_meta - 1)
WHERE
	options.user_id      IS NULL AND
	options.post_id      IS NULL AND
	options.option_cat   = 'termusages_detail' AND
	options.option_key   = 'usage_count' AND
	options.option_value = OLD.termusage_foreign;

END