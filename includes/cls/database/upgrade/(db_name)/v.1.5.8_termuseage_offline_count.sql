-- multi_query
CREATE TRIGGER `termusages_update_options_record_offline_count_on_update` AFTER UPDATE ON `termusages` FOR EACH ROW
BEGIN

INSERT INTO
	options
SET
	options.user_id      = NULL,
	options.post_id      = NULL,
	options.option_cat   = 'termusages_detail',
	options.option_key   = 'usage_count',
	options.option_value = NEW.termusage_foreign,
	options.option_meta  = 1
ON DUPLICATE KEY UPDATE
	options.option_meta = IF(options.option_meta IS NULL OR options.option_meta = '', 1, options.option_meta + 1);

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