<?php
namespace lib\db;

class filters
{

	/**
	 * get supoort filters
	 * this array exist in to pollstat table
	 * when inserting new value to this table check field
	 * all field of that table is here
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public static function support_filter($_check = null)
	{

		$support_filter =
		[
			'gender',
			'marrital_status',
			'parental_status',
			'exercise_habits',
			'employment_status',
			'business_owner',
			'industry',
			'devices_owned',
			'internet_usage',
			'birthdate',
			'age',
			'range',
			'graduation',
			'course',
			'country_birth',
			'country',
			'province_birth',
			'province',
			'birthcity',
			'city',
			'language',
			'meta'
		];

		if($_check)
		{
			if(in_array($_check, $support_filter))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return $support_filter;
	}


	/**
	 * return category of filter
	 *
	 * @param      <type>  $_cat   The cat
	 */
	public static function filter_cat($_cat)
	{
		$cat = null;

		switch ($_cat) {
			case 'gender':
				$cat = "public";
				break;

			case 'city':
			case 'privince':
			case 'country':
			case 'country_birth':
				$cat = "city";
				break;

			default:
				$cat = "other";
				break;
		}

		return $cat;
	}

	/**
	 * get all user detail and make page of set filter
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_exist_filter()
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
		$result = \lib\db::get($query);

		$filters = [];
		foreach ($result as $key => $value)
		{
			$cat = self::filter_cat($value['key']);
			if(!isset($filters[$cat]))
			{
				$filters[$cat] = [];
			}

			if(!isset($filters[$cat][$value['key']]))
			{
				$filters[$cat][$value['key']] = [$value['value']];
			}
			else
			{
				array_push($filters[$cat][$value['key']], $value['value']);
			}
		}
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
	public static function insert($_poll_id, $_args)
	{
		if($_poll_id)
		{
			$poll_id = $_poll_id;
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


	/**
	 * get filter list and reutnr count number of member by this filter
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function count_filtered_member($_args)
	{
		// we muse for every filter join to get num of all
		$join     = [];
		$where    = [];
		$where [] = " main.user_id IS NOT NULL ";
		$where [] = " main.post_id IS NULL ";
		$where [] = " main.option_cat LIKE 'user_detail%' ";

		foreach ($_args as $key => $value) {
			$join[] = " INNER JOIN options as `$key` ON main.user_id = $key.user_id ";
			if(is_array($value))
			{
				foreach ($value as $index => $filter) {
					$where[] = " ($key.option_key = '$key' and $key.option_value = '$filter') ";
				}
			}
			else
			{
				$where[] = " ($key.option_key = '$key' and $key.option_value = '$value') ";
			}
		}
		$join = join($join, "\n");
		$where = join($where, " AND \n");
		$query =
		"
			SELECT
				main.user_id
			FROM
				options as `main`
				$join
			WHERE
				$where
			GROUP BY
				main.user_id
		";
		$result = \lib\db::query($query);
		$num  = \lib\db::num();
		return $num;
	}
}