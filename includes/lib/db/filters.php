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
			'marrital',
			'birthday',
			'age',
			'language',
			'graduation',
			'course',
			'employment',
			'business',
			'industry',
			'countrybirth',
			'provincebirth',
			'citybirth',
			'country',
			'province',
			'city',
			'parental',
			'exercise',
			'devices',
			'internetusage'
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
			// public
			case 'gender':
			case 'marrital_status':
			case 'language':
			case 'employment_status':
				$cat = "public";
				break;

			// education
			case 'graduation':
			case 'course':
				$cat = "education";
				break;

			// family
			case 'parental_status':
			case 'birthdate':
			case 'age':
			case 'range':
				$cat = "family";
				break;

			// job
			case 'business_owner':
			case 'industry':
				$cat = "job";
				break;

			// location
			case 'province':
			case 'city':
			case 'country':
			case 'birthcity':
			case 'province_birth':
			case 'country_birth':
				$cat = "location";
				break;

			// favorites
			case 'favorites':
			case 'exercise_habits':
				$cat = "favorites";
				break;

			// other
			case 'devices_owned':
			case 'internet_usage':
				$cat = "other";

			// other
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
			-- filters::get_exist_filter()
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

		// for sort categories
		$sort =
		[
			"public",
			"education",
			"family",
			"job",
			"location",
			"favorites",
			"other"
		];
		$sorted_filter = [];
		// sort filter by sort array
		foreach ($sort as $key => $value) {
			if(isset($filters[$value]))
			{
				$sorted_filter[$value] = $filters[$value];
			}
		}
		return $sorted_filter;
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
				-- filters::get_poll_filter()
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
		// we support 5 filter
		if(count($_args) > 5)
		{
			return false;
		}
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
			-- filters::count_filtered_member()
		";
		$result = \lib\db::query($query);
		$num  = \lib\db::num();
		return $num;
	}
}