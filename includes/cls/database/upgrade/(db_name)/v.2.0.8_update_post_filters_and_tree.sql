INSERT INTO polltrees
(
	post_id,
	parent,
	opt
)

SELECT
	options.post_id,
	replace(options.option_key, 'tree_', ''),
	IF(options.option_value = 'true', NULL, options.option_value)
FROM
	options
WHERE
	options.post_id IS NOT NULL AND
	options.option_key LIKE 'tree%' AND
	options.option_cat    = CONCAT('poll_' , options.post_id) AND
	options.option_status = 'enable'
