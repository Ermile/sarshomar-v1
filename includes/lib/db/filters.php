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
			'gender'        => ['male', 'female'],
			'marrital'      => ['single', 'marriade'],
			'birthday'      => null,
			'age'           => null,
			'language'      => null,
			'graduation'    => null,
			'course'        => null,
			'employment'    => null,
			'business'      => null,
			'industry'      => null,
			'countrybirth'  => null,
			'provincebirth' => null,
			'citybirth'     => null,
			'country'       => null,
			'province'      => null,
			'city'          => null,
			'parental'      => null,
			'exercise'      => null,
			'devices'       => null,
			'internetusage' => null
		];

		if($_check)
		{
			if(array_key_exists($_check, $support_filter))
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
	 * insert new tag in filters table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{
		if(empty($_args))
		{
			return null;
		}
		$set = [];
		foreach ($_args as $key => $value) {
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			else
			{
				$set[] = " `$key` = '$value' ";
			}
		}
		$set = join($set, ',');

		// set the unique field
		$unique = json_encode($_args, JSON_UNESCAPED_UNICODE);

		$query =
		"
			INSERT INTO
				filters
			SET
				$set,
				`unique` = '$unique'
		";
		$result = \lib\db::query($query);
		// return the insert id
		return \lib\db::insert_id();
	}


	/**
	 * check exist the filter
	 * get the id of filters
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function check($_args, $_field = null)
	{
		// check arguments
		if(!is_array($_args))
		{
			return false;
		}

		// check arguments
		if(empty($_args))
		{
			return null;
		}

		// make where
		$where = [];
		foreach ($_args as $key => $value) {
			$where[] = " `$key` = '$value' ";
		}
		$where = join($where, " AND ");

		// check need field and make get field
		$field     = 'id';
		$get_field = "id";
		if(is_array($_field))
		{
			$field     = '`'. join($_field, '`, `'). '`';
			$get_field = null;
		}
		elseif($_field && is_string($_field))
		{
			$field     = '`'. $_field. '`';
			$get_field = $_field;
		}

		$query =
		"
			SELECT
				$field
			FROM
				filters
			WHERE
				$where
			LIMIT 1
		";

		$result = \lib\db::get($query, $get_field, true);

		// return true if no field need and filter exist
		if(!empty($result) && !$_field)
		{
			return true;
		}
		return $result;
	}


	/**
	 * get the id of filters table
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The identifier.
	 */
	public static function get_id($_args)
	{
		return self::check($_args, "id");
	}


	/**
	 * get the value of filters
	 *
	 * @param      <type>  $_fielter_id  The fielter identifier
	 * @param      string  $_field       The field
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_fielter_id, $_field = null)
	{

		$field     = '*';
		$get_field = null;
		if(is_array($_field))
		{
			$field     = '`'. join($_field, '`, `'). '`';
			$get_field = null;
		}
		elseif($_field && is_string($_field))
		{
			$field     = '`'. $_field. '`';
			$get_field = $_field;
		}

		$query =
		"
			SELECT
				$field
			FROM
				filters
			WHERE
				filters.id = $_fielter_id
			LIMIT 1
		";
		$result = \lib\db::get($query, $get_field, true);
		return $result;
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
			case 'marrital':
			case 'language':
			case 'employment':
				$cat = "public";
				break;

			// education
			case 'graduation':
			case 'course':
				$cat = "education";
				break;

			// family
			case 'parental':
			case 'birthdate':
			case 'age':
			case 'range':
				$cat = "family";
				break;

			// job
			case 'business':
			case 'industry':
				$cat = "job";
				break;

			// location
			case 'province':
			case 'city':
			case 'country':
			case 'citybirth':
			case 'provincebirth':
			case 'countrybirth':
				$cat = "location";
				break;

			// favorites
			case 'favorites':
			case 'exercise':
				$cat = "favorites";
				break;

			// other
			case 'devices':
			case 'internet':
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
		// muse be edit by new syntax of filters

		return false;

		// $query =
		// "
		// 	SELECT
		// 		options.option_key 		AS 'key',
		// 		options.option_value 	AS 'value'
		// 	FROM
		// 		options
		// 	WHERE
		// 		options.post_id IS NULL AND
		// 		options.user_id IS NOT NULL AND
		// 		options.option_cat LIKE 'user_detail_%'
		// 	GROUP BY
		// 		options.option_key,
		// 		options.option_value
		// 	-- filters::get_exist_filter()
		// ";
		// $result = \lib\db::get($query);


		// $filters = [];
		// foreach ($result as $key => $value)
		// {
		// 	$cat = self::filter_cat($value['key']);
		// 	if(!isset($filters[$cat]))
		// 	{
		// 		$filters[$cat] = [];
		// 	}

		// 	if(!isset($filters[$cat][$value['key']]))
		// 	{
		// 		$filters[$cat][$value['key']] = [$value['value']];
		// 	}
		// 	else
		// 	{
		// 		array_push($filters[$cat][$value['key']], $value['value']);
		// 	}
		// }

		// // for sort categories
		// $sort =
		// [
		// 	"public",
		// 	"education",
		// 	"family",
		// 	"job",
		// 	"location",
		// 	"favorites",
		// 	"other"
		// ];
		// $sorted_filter = [];
		// // sort filter by sort array
		// foreach ($sort as $key => $value) {
		// 	if(isset($filters[$value]))
		// 	{
		// 		$sorted_filter[$value] = $filters[$value];
		// 	}
		// }
		// return $sorted_filter;
	}


	/**
	 * Gets the poll filter.
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function get_poll_filter($_poll_id)
	{
		// muse be edit by new syntax of filters
		return false;

		// $query =
		// "
		// 	SELECT
		// 		options.option_key 		AS 'key',
		// 		options.option_value 	AS 'value'
		// 	FROM
		// 		options
		// 	WHERE
		// 		options.post_id = $_poll_id AND
		// 		options.user_id IS NULL AND
		// 		options.option_cat LIKE 'poll_$_poll_id' AND
		// 		options.option_key NOT IN ('stat') AND
		// 		options.option_key NOT LIKE 'opt_%' AND
		// 		options.option_key NOT LIKE 'answer_%' AND
		// 		options.option_key NOT LIKE 'tree_%'
		// 		-- filters::get_poll_filter()
		// ";
		// $result = \lib\db::get($query);
		// return $result;
	}


	/**
	 * get filter list and reutnr count number of member by this filter
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function count_filtered_member($_args)
	{
		// muse be edit by new syntax of filters
		return false;

		// // we support 5 filter
		// if(count($_args) > 5)
		// {
		// 	return false;
		// }
		// // we muse for every filter join to get num of all
		// $join     = [];
		// $where    = [];
		// $where [] = " main.user_id IS NOT NULL ";
		// $where [] = " main.post_id IS NULL ";
		// $where [] = " main.option_cat LIKE 'user_detail%' ";

		// foreach ($_args as $key => $value) {
		// 	$join[] = " INNER JOIN options as `$key` ON main.user_id = $key.user_id ";
		// 	if(is_array($value))
		// 	{
		// 		foreach ($value as $index => $filter) {
		// 			$where[] = " ($key.option_key = '$key' and $key.option_value = '$filter') ";
		// 		}
		// 	}
		// 	else
		// 	{
		// 		$where[] = " ($key.option_key = '$key' and $key.option_value = '$value') ";
		// 	}
		// }
		// $join = join($join, "\n");
		// $where = join($where, " AND \n");
		// $query =
		// "
		// 	SELECT
		// 		main.user_id
		// 	FROM
		// 		options as `main`
		// 		$join
		// 	WHERE
		// 		$where
		// 	GROUP BY
		// 		main.user_id
		// 	-- filters::count_filtered_member()
		// ";
		// $result = \lib\db::query($query);
		// $num  = \lib\db::num();
		// return $num;
	}
}