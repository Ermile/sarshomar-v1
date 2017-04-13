<?php
namespace lib\db;

/** ranks managing **/
class ranks
{
	use \lib\utility\money;

	public static $fields = "
		ranks.*,
		posts.post_url as `url`,
		posts.post_title as `title`
		FROM ranks
		LEFT JOIN posts on ranks.post_id = posts.id
		";
	/**
	 * insert new record of ranks table
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      array   $_field    The field
	 */
	public static function set($_poll_id, $_field = [])
	{
		if(!$_poll_id)
		{
			return false;
		}

		$default_fields = self::$poll_ranks_value;
		$default_fields = array_map(function(){ return 0; } , $default_fields);
		if(isset($default_fields['createdate']))
		{
			$default_fields['createdate'] = date("Y-m-d H:i:s");
		}

		$_field = array_merge($default_fields, $_field);

		$set = [];
		foreach ($_field as $field => $value)
		{
			if($value === null)
			{
				$set[] = " ranks.$field = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " ranks.$field = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " ranks.$field = '$value' ";
			}
		}

		$set = implode(",", $set);

		$query = "INSERT INTO ranks	SET	ranks.post_id = $_poll_id, $set ";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * get the ranks of pollls
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_poll_id, $_field = null)
	{
		if(!$_poll_id)
		{
			return false;
		}

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

		$query = " SELECT $field FROM ranks WHERE ranks.post_id = $_poll_id LIMIT 1	-- ranks::get()	";
		$result = \lib\db::get($query, $get_field, true);
		return $result;

	}


	/**
	 * change poll ranks
	 *
	 * @param      <type>   $_poll_id  The poll identifier
	 * @param      <type>   $_field    The field
	 * @param      integer  $_plus     The plus
	 * @param      array    $_options  The options
	 *
	 * @return     <type>   ( description_of_the_return_value )
	 */
	public static function change_rank($_poll_id, $_field, $_plus = 1, $_options = [])
	{

		if(!$_poll_id)
		{
			return false;
		}

		$default_options =
		[
			'replace' => false,
			'type'    => 'plus',
		];

		$_options        = array_merge($default_options, $_options);

		$plus  = true;
		$minus = false;

		if($_options['type'] === 'minus')
		{
			$plus  = false;
			$minus = true;
		}

		$replace = false;
		if($_options['replace'] === true)
		{
			$replace = true;
		}

		$post_rank = self::get($_poll_id);

		if(empty($post_rank))
		{
			self::set($_poll_id);
			$post_rank               = self::$poll_ranks_value;
			$post_rank               = array_map(function(){ return 0; }, $post_rank);
			$post_rank['createdate'] = date("Y-m-d");
			$post_rank['value']      = 0;
		}

		$sum    = 0;
		$update = [];
		foreach ($post_rank as $key => $value)
		{
			switch ($key)
			{
				case 'createdate':
					$now       = time();
					$your_date = strtotime($value);
					$datediff  = $now - $your_date;
					$ago       =  intval($datediff / (60 * 60 * 24));
					if(isset($post_rank['ago']) && $post_rank['ago'] != $ago)
					{
						$sum      += (intval($ago) * intval(self::$poll_ranks_value['ago'][1]));
						$update[] = " ranks.ago = $ago ";
					}
					break;

				case 'public':
					if($replace)
					{
						if(intval($_plus) == 0 || intval($_plus) == 1)
						{
							$value    = intval($_plus);
							$update[] = " ranks.$key = $_plus ";
						}
					}
					else
					{
						$value = intval($value) + intval($_plus);
						if($value > 1)
						{
							$value = 1;
						}
						$update[] = " ranks.$key = $value ";
					}
					break;

				default:
					if($key == $_field)
					{
						if($replace)
						{
							$value    = intval($_plus);
							if($value < 0)
							{
								$value = 0;
							}
							$update[] = " ranks.$key = $value ";
						}
						else
						{
							if($plus)
							{
								$value    = intval($value) + intval($_plus);
								$update[] = " ranks.$key = ranks.$key + 1 ";
							}
							else
							{
								$value    = intval($value) - intval($_plus);
								if($value <= 0)
								{
									$update[] = " ranks.$key = 0 ";
									$value = 0;
								}
								else
								{
									$update[] = " ranks.$key = ranks.$key - 1 ";
								}
							}
						}
					}

					if(array_key_exists($key, self::$poll_ranks_value))
					{
						if(self::$poll_ranks_value[$key][0] === true)
						{
							$sum += (intval($value) * intval(self::$poll_ranks_value[$key][1]));
						}
						elseif(self::$poll_ranks_value[$key][0] === false)
						{
							$sum -= (intval($value) * intval(self::$poll_ranks_value[$key][1]));
						}
					}
					break;
			}
		}

		$update[] = " ranks.value = $sum ";

		$update = implode(",", $update);
		$query  = " UPDATE ranks SET $update WHERE post_id = $_poll_id LIMIT 1";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * plus the field of ranks
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      <type>  $_field    The field
	 */
	public static function plus($_poll_id, $_field, $_plus = 1, $_options = [])
	{
		$default_options = ['type' => 'plus'];
		if(!is_array($_options))
		{
			$_options = [];
		}
		$_options = array_merge($default_options, $_options);
		return self::change_rank($_poll_id, $_field, $_plus, $_options);
	}


	/**
	 * minus the poll ranks
	 *
	 * @param      <type>   $_poll_id  The poll identifier
	 * @param      <type>   $_field    The field
	 * @param      integer  $_minus    The minus
	 * @param      array    $_options  The options
	 *
	 * @return     <type>   ( description_of_the_return_value )
	 */
	public static function minus($_poll_id, $_field, $_minus = 1, $_options = [])
	{
		$default_options = ['type' => 'minus'];
		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);
		return self::change_rank($_poll_id, $_field, $_minus, $_options);
	}


	/**
	 * Searches for the first match.
	 *
	 * @param      <type>  $_string   The string
	 * @param      array   $_options  The options
	 */
	public static function search($_string = null, $_options = [])
	{
		$where = []; // conditions

		if(!$_string && empty($_options))
		{
			// default return of this function 10 last record of poll
			$_options['get_last'] = true;
		}

		$default_options =
		[
			// just return the count record
			"get_count"   => false,
			// enable|disable paignation,
			"pagenation"  => true,
			// for example in get_count mode we needless to limit and pagenation
			// default limit of record is 15
			// set the limit  = null and pagenation = false to get all record whitout limit
			"limit"           => 15,
			// for manual pagenation set the statrt_limit and end limit
			"start_limit"     => 0,
			// for manual pagenation set the statrt_limit and end limit
			"end_limit"       => 10,
			// the the last record inserted to post table
			"get_last"        => false,
			// default order by ASC you can change to DESC
			"order"           => "ASC",
			// custom sort by field
			"sort"			  => null,
		];
		$_options = array_merge($default_options, $_options);

		$pagenation = false;
		if($_options['pagenation'])
		{
			// page nation
			$pagenation = true;
		}

		// ------------------ get count
		$only_one_value = false;
		$get_count      = false;

		if($_options['get_count'] === true)
		{
			$get_count      = true;
			$public_fields  = " COUNT(*) AS 'rankscount' FROM ranks ";
			$limit          = null;
			$only_one_value = true;
		}
		else
		{
			$limit         = null;
			$public_fields = self::$fields;
			if($_options['limit'])
			{
				$limit = $_options['limit'];
			}
		}

		// ------------------ get last
		$order = null;
		if($_options['get_last'])
		{
			if($_options['sort'])
			{
				$order = " ORDER BY ranks.$_options[sort] $_options[order] ";
			}
			else
			{
				$order = " ORDER BY ranks.id DESC ";
			}
		}
		else
		{
			if($_options['sort'])
			{
				$order = " ORDER BY ranks.$_options[sort] $_options[order] ";
			}
			else
			{
				$order = " ORDER BY ranks.id $_options[order] ";
			}
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];

		unset($_options['pagenation']);
		unset($_options['get_count']);
		unset($_options['limit']);
		unset($_options['start_limit']);
		unset($_options['end_limit']);
		unset($_options['get_last']);
		unset($_options['order']);
		unset($_options['sort']);

		foreach ($_options as $key => $value)
		{
			if(is_array($value))
			{
				if(isset($value[0]) && isset($value[1]) && is_string($value[0]) && is_string($value[1]))
				{
					// for similar "ranks.`field` LIKE '%valud%'"
					$where[] = " ranks.`$key` $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " ranks.`$key` IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " ranks.`$key` = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " ranks.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");
		$search = null;
		if($_string != null)
		{
			// $search =
			// "(

			// )";
			// if($where)
			// {
			// 	$search = " AND ". $search;
			// }
		}

		if($where)
		{
			$where = "WHERE $where";
		}
		elseif($search)
		{
			$where = "WHERE";
		}

		if($pagenation && !$get_count)
		{
			$pagenation_query = (int) \lib\db::get("SELECT COUNT(*) AS `count` FROM ranks $where $search -- get count log for pagenation", 'count', true);
			list($limit_start, $limit) = \lib\db::pagnation($pagenation_query, $limit);
			$limit = " LIMIT $limit_start, $limit ";
		}
		else
		{
			// in get count mode the $limit is null
			if($limit)
			{
				$limit = " LIMIT $start_limit, $end_limit ";
			}
		}

		$json = json_encode(func_get_args());
		$query = " SELECT $public_fields $where	$search	$order $limit	-- ranks::search()	-- $json";

		if(!$only_one_value)
		{
			$result = \lib\db::get($query, null, false);
			$result = \lib\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \lib\db::get($query, 'rankscount', true);
		}

		return $result;
	}
}
?>