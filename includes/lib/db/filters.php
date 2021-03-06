<?php
namespace lib\db;

class filters
{
	use \lib\utility\money;

	public static $FILTERS = [];
	/**
	 * get supoort filters
	 * this array exist in to pollstat table
	 * when inserting new value to this table check field
	 * all field of that table is here
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public static function support_filter($_check = null, $_value = null)
	{
		$support_filter =
		[
			'gender'           => ['male', 'female'],
			'marrital'         => ['single', 'married'],
			'internetusage'    => ['low', 'mid', 'high'],
			'graduation'       => ['illiterate', 'undergraduate', 'graduate'],
			'degree'           => ['under diploma', 'diploma', '2 year college', 'bachelor', 'master', 'phd'],
			'course'           => null,
			'age'              => null,
			'range'            => ['-13', '14-17', '18-24', '25-30', '31-44', '45-59', '60+'],
			'country'          => null,
			'province'         => null,
			'city'             => null,
			'employmentstatus' => ['employee', 'unemployed', 'retired'],
			'housestatus'      => ['owner', 'tenant', 'homeless'],
			'religion'         => null,
			'language'         => null,
			'industry'         => null
		];

		if($_check)
		{
			if($_value)
			{
				if(array_key_exists($_check, $support_filter))
				{
					if(is_array($_value))
					{
						$check = true;
						foreach ($_value as $key => $value)
						{
							if(!in_array($value, $support_filter[$_check]))
							{
								$check = false;
							}
						}
						return $check;
					}
					else
					{
						if(is_array($support_filter[$_check]) && in_array($_value, $support_filter[$_check]))
						{
							return true;
						}
						elseif($support_filter[$_check] === null)
						{
							return null;
						}
						else
						{
							return false;
						}

					}
				}
				else
				{
					return false;
				}
			}
			elseif(array_key_exists($_check, $support_filter))
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
	 * the mony filter
	 *
	 * @param      <type>  $_filter  The filter
	 */
	public static function money_filter($_filter = null)
	{
		if($_filter)
		{
			if(isset(self::$money_filter[$_filter]))
			{
				return self::$money_filter[$_filter];
			}
			else
			{
				return 0;
			}
		}
		return self::$money_filter;
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

		$exits_record_id = [];

		$where = self::extract_filter($_args, true);
		$fields = [];
		$values = [];
		foreach ($where as $key => $value)
		{
			if($fields === [])
			{
				$tmp = array_keys($value);
				foreach ($tmp as $k => $v)
				{
					array_push($fields, "`$v`");
				}
				$fields = implode(" , ", $fields);

			}
			$check_exits_record = self::check($value, 'id');
			if(!empty($check_exits_record))
			{
				array_push($exits_record_id, (int) $check_exits_record);
			}

			if(empty($check_exits_record))
			{
				$check_null = array_filter($value);
				if(!empty($check_null))
				{
					$tmp = [];
					foreach ($value as $k => $v)
					{
						if($v === null)
						{
							array_push($tmp, "NULL");
						}
						else
						{
							array_push($tmp, "'$v'");
						}
					}
					$values[] = implode(" , ", $tmp);
				}
			}
		}

		$count_inserted_record = count($values);
		if(empty($values))
		{

			return array_unique($exits_record_id);
		}

		$values = join($values, '), (');
		$query =
		"
			INSERT INTO
				filters
			($fields)
			VALUES
			($values)
		";

		$result = \lib\db::query($query);
		// return the insert id
		$last_insert_id =  \lib\db::insert_id();

		if($count_inserted_record === 1)
		{
			return $last_insert_id;
		}
		elseif($count_inserted_record > 1)
		{
			for ($i = 1; $i <= $count_inserted_record; $i++)
			{
				array_push($exits_record_id, $last_insert_id++);
			}
			return array_unique($exits_record_id);
		}
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
		$support_filter = self::support_filter();
		$support_filter = array_keys($support_filter);

		foreach ($support_filter as $key => $value)
		{
			if(isset($_args[$value]))
			{
				$where[] = " `$value` = '$_args[$value]' ";
			}
			else
			{
				$where[] = " `$value` IS NULL ";
			}
		}

		if(empty($where))
		{
			return null;
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
		$result = self::check($_args, "id");
		if(empty($result))
		{
			return 0;
		}
		return $result;
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
		$_fielter_id = intval($_fielter_id);
		if(!isset(self::$FILTERS[$_fielter_id]))
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
			self::$FILTERS[$_fielter_id] = $result;
		}

		return self::$FILTERS[$_fielter_id];
	}


	/**
	 * return category of filter
	 *
	 * @param      <type>  $_cat   The cat
	 */
	public static function filter_cat($_cat)
	{
		$cat = null;

		switch ($_cat)
		{
			// public
			case 'gender':
			case 'marrital':
			case 'language':
				$cat = "public";
				break;

			// education
			case 'graduation':
			case 'course':
			case 'degree':
				$cat = "education";
				break;

			// family
			case 'age':
			case 'range':
				$cat = "family";
				break;

			// job
			case 'employmentstatus':
			case 'industry':
				$cat = "job";
				break;

			// location
			case 'province':
			case 'city':
			case 'country':
			case 'housestatus':
				$cat = "location";
				break;

			// other
			case 'internetusage':
			case 'religion':
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
		$support_filter = self::support_filter();
		$filters = [];
		foreach ($support_filter as $key => $value)
		{
			$filters[self::filter_cat($key)][$key] = $value;
		}

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
		foreach ($sort as $key => $value)
		{
			if(isset($filters[$value]))
			{
				$sorted_filter[$value] = $filters[$value];
			}
		}

		return $sorted_filter;
	}


	/**
	 * get filter list and reutnr count number of member by this filter
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function count_user($_args)
	{
		if(!is_array($_args))
		{
			return false;
		}

		$filter_query = self::search($_args, true, true);

		if($filter_query)
		{
			$query = "SELECT SUM(filters.count) AS `count` FROM filters WHERE $filter_query ";
			$count = \lib\db::get($query, 'count', true);
			return $count;
		}
		else
		{
			$count = \saloos::lib_static('db')->users()::get_count('available');
			return $count;
		}
	}


	/**
	 * Searches for the first match.
	 *
	 * @param      <type>         $_filters             The filters
	 * @param      boolean        $_else_fiels_is_null  The else fiels is null
	 * @param      boolean        $_return_raw          The return raw
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function search($_filters, $_else_fiels_is_null = false, $_return_raw = false)
	{
		if(!is_array($_filters))
		{
			return false;
		}

		$where = [];
		if($_else_fiels_is_null)
		{
			foreach ($_filters as $key => $value)
			{
				if(self::support_filter($key))
				{
					if(is_array($value))
					{
						$or = [];
						foreach ($value as $k => $v)
						{
							$or[] = " `$key` = '$v' ";
						}
						$or = join($or, " OR ");
						$where[] = " ( $or ) ";
					}
					else
					{
						$where[] = " `$key` = '$value' ";
					}
				}
			}
			$where = join($where, " AND ");
		}
		else
		{
			$where = self::extract_filter($_filters, false);
		}
		if(!$where)
		{
			return null;
		}

		if($_return_raw)
		{
			return $where;
		}
		else
		{
			$query = "SELECT id	FROM filters WHERE $where ";
			return \lib\db::get($query, 'id');
		}
	}


	/**
	 * extract filters
	 *
	 * @param      <type>         $_filters  The filters
	 * @param      string         $_result   The result
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	private static function extract_filter($_filters, $_return_array = true)
	{
		if(!is_array($_filters))
		{
			return false;
		}
		//
		//---------------------------------------
		//[
		// 	degree  => diploma,
		// 	gender  => [male,  female],
		// 	marital => [single, marid]
		//]
		//---------------------------------------
		//[
		// 	0 => [gender => male, marital => single, degree => diploma, range => NULL, age => NULL, ... ],
		// 	1 => [gender => female, marital => single,degree => diploma, range => NULL, age => NULL, ... ],
		// 	2 => [gender => male,   marital => marid, degree => diploma, range => NULL, age => NULL, ... ],
		// 	3 => [gender => female, marital => marid, degree => diploma, range => NULL, age => NULL, ... ]
		//]
		//---------------------------------------
		// SELECT// FROM filters WHERE
		// (gender = male   AND marital = single AND degree = diploma AND age IS NULL) OR
		// (gender = female AND marital = single AND degree = diploma AND age IS NULL) OR
		// (gender = male   AND marital = marid  AND degree = diploma AND age IS NULL) OR
		// (gender = female AND marital = marid  AND degree = diploma AND age IS NULL)
		//---------------------------------------
		//
		$where        = [];
		$rows         = [];
		$i            = 0;
		$clone        = array_keys(self::support_filter());
		$clone        = array_flip($clone);
		$clone        = array_map(function(){return null;}, $clone); // set all index of array null
		$sum          = array_map(function($_a){return count($_a);}, $_filters);
		$count_record = empty($_filters) ? 0 : 1;
		foreach ($sum as $filter => $count)
		{
			$count_record *= $count;
		}

		$index       = 0;
		$where_array = [];
		for ($i = 0; $i < $count_record; $i++)
		{
			$where_array[$i] = $clone;
		}

		foreach ($_filters as $filter => $value)
		{
			if(is_array($value))
			{
				for ($i = 0; $i < $count_record; $i++)
				{
					foreach ($value as $k => $v)
					{
						$where_array[$i][$filter] = $v;
						$i++;
					}
					$i--;
				}
			}
			else
			{
				for ($i = 0; $i < $count_record; $i++)
				{
					$where_array[$i][$filter] = $value;
				}
			}
		}
		$or = [];
		foreach ($where_array as $key => $value)
		{
			$and = [];
			foreach ($value as $k => $v)
			{
				if($v === null)
				{
					$and[] = " `$k` IS NULL ";
				}
				else
				{
					$and[] = " `$k` = '$v' ";
				}
			}
			$or[] = '('. join($and, "AND"). ')';
		}
		$where = join($or, " OR ");

		if($_return_array)
		{
			return $where_array;
		}
		else
		{
			return $where;
		}
	}
}
?>