<?php
namespace lib\db;

class filters
{
	public static function get()
	{
		$query =
		"
			SELECT
				options.option_key 		AS 'key',
				options.option_value 	AS 'value'
			FROM
				options
			WHERE
				options.post_id IS NULL AND
				options.user_id IS NOT NULL AND
				options.option_cat LIKE 'user_detail_%'
			GROUP BY
				options.option_key,
				options.option_value
		";

		return \lib\db::get($query);
	}


	public static function insert($_args)
	{
		if(isset($_args['poll_id']))
		{
			$poll_id = $_args['poll_id'];
			unset($_args['poll_id']);
		}
		else
		{
			\lib\debug::error(T_("poll id can not be null"));
			return false;
		}
		$field_value = [];
		foreach ($_args as $key => $value) {
			$field_value[] =
			[
				'post_id' => $poll_id,
				'option_cat' => "poll_$poll_id",
				'option_key' => $key,
				'option_value' => $value,
				'option_meta' => null,
			];
		}

		return \lib\db\options::insert_multi($field_value);
	}
}