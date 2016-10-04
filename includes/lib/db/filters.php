<?php
namespace lib\db;

class filters
{
	/**
	 * get all user detail and make page of set filter
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get()
	{

		$filters =
		[
			"public" =>
			[
				"min_member",
				"max_member",
				"age_range",
				"gender"
			],
			"education" =>
			[
				"illiterate",
				"illetrate",
				"With literacy"
			],
			"family" =>
			[
				"single",
				"marriade"
			]
		];
		return $filters;
	}


	/**
	 * Gets the poll filter.
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function get_poll_filter($_poll_id)
	{
		$query =
		"
			SELECT
				options.option_key 		AS 'key',
				options.option_value 	AS 'value'
			FROM
				options
			WHERE
				options.post_id = $_poll_id AND
				options.user_id IS NULL AND
				options.option_cat LIKE 'poll_$_poll_id' AND
				options.option_key NOT IN ('stat') AND
				options.option_key NOT LIKE 'opt_%' AND
				options.option_key NOT LIKE 'answer_%' AND
				options.option_key NOT LIKE 'tree_%'
		";

		$result = \lib\db::get($query);
		return $result;
	}


	/**
	 * add filter to poll
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function insert($_args)
	{
		if(isset($_args['poll_id']))
		{
			$poll_id = $_args['poll_id'];
			unset($_args['poll_id']);
		}
		else
		{
			return false;
		}

		$field_value = [];
		foreach ($_args as $key => $value) {
			$field_value[] =
			[
				'post_id'      => $poll_id,
				'option_cat'   => "poll_$poll_id",
				'option_key'   => $key,
				'option_value' => $value,
				'option_meta'  => null,
			];
		}

		return \lib\db\options::insert_multi($field_value);
	}
}