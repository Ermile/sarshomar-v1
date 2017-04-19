<?php
namespace lib\db;

/** userdashboards managing **/
class userdashboards
{
	/**
	 * user dashboard field
	 *
	 * @var        array
	 */
	public static $dashboard_field =
	[
		'poll_answered',
		'poll_skipped',
		'survey_answered',
		'survey_skipped',
		'my_poll',
		'my_survey',
		'my_poll_answered',
		'my_poll_skipped',
		'my_survey_answered',
		'my_survey_skipped',
		'user_referred',
		'user_verified',
		'comment_count',
		'draft_count',
		'publish_count',
		'awaiting_count',
		'my_like',
		'my_fav',
	];


	public static $fields =
	"
	 	users.id 					AS `id`,
	 	users.user_mobile 					AS `user_mobile`,
	 	users.user_username 					AS `user_username`,
	 	users.user_displayname 					AS `user_displayname`,
	 	users.user_status 					AS `user_status`,
	 	users.user_validstatus 					AS `user_validstatus`,
	 	users.user_port 					AS `user_port`,
	 	users.user_trust 					AS `user_trust`,
	 	users.user_verify 					AS `user_verify`,
	 	users.user_language 					AS `user_language`,
	 	userdashboards.poll_answered 					AS `poll_answered`,
	 	userdashboards.poll_skipped 					AS `poll_skipped`,
	 	userdashboards.my_poll 					AS `my_poll`,
	 	userdashboards.my_poll_answered 					AS `my_poll_answered`,
	 	userdashboards.my_poll_skipped 					AS `my_poll_skipped`,
	 	userdashboards.user_referred 					AS `user_referred`,
	 	userdashboards.user_verified 					AS `user_verified`,
	 	userdashboards.comment_count 					AS `comment_count`,
	 	userdashboards.draft_count 					AS `draft_count`,
	 	userdashboards.publish_count 					AS `publish_count`,
	 	userdashboards.awaiting_count 					AS `awaiting_count`,
	 	userdashboards.my_fav 					AS `my_fav`,
	 	userdashboards.my_like 					AS `my_like`,
		(SELECT SUM(transactions.budget) FROM transactions
		INNER JOIN units ON transactions.unit_id = units.id
		WHERE transactions.id IN (
		SELECT MAX(transactions.id) FROM transactions
		WHERE transactions.user_id = users.id
		GROUP BY transactions.unit_id)) AS `budget`
		FROM
			users
		LEFT JOIN userdashboards ON userdashboards.user_id = users.id
	";


	/**
	 * set first record of user dashboard
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function set($_user_id)
	{
		$fields = self::$dashboard_field;

		$set = [];
		foreach ($fields as $key => $value)
		{
			$set[] = " userdashboards.$value = NULL ";
		}

		$set = implode(",", $set);

		$query = "INSERT INTO userdashboards SET userdashboards.user_id = $_user_id, $set ";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * get dashboard data
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      string  $_field    The field
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_user_id, $_field = null)
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

		$query = "SELECT $field FROM userdashboards WHERE userdashboards.user_id = $_user_id LIMIT 1 -- userdashboards::get() ";
		$result = \lib\db::get($query, $get_field, true);
		return $result;
	}


	/**
	 * change poll userdashboards
	 *
	 * @param      <type>   $_user_id  The poll identifier
	 * @param      <type>   $_field    The field
	 * @param      integer  $_plus     The plus
	 * @param      array    $_options  The options
	 *
	 * @return     <type>   ( description_of_the_return_value )
	 */
	public static function change_dashboard($_user_id, $_field, $_plus = 1, $_options = [])
	{
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

		$userdashboards = self::get($_user_id);

		if(empty($userdashboards))
		{
			self::set($_user_id);
			$userdashboards               = self::$dashboard_field;
		}

		$sum    = 0;
		$update = [];
		foreach ($userdashboards as $key => $value)
		{
			switch ($key)
			{
				case 'id':
				case 'user_id':
					continue;
					break;

				default:
					if($key == $_field)
					{
						if($replace)
						{
							if($_plus < 0)
							{
								$_plus = 0;
							}
							$update[] = " userdashboards.$key = $_plus ";
						}
						else
						{
							if($plus)
							{
								if(is_null($value))
								{
									$update[] = " userdashboards.$key = 1 ";
								}
								else
								{
									$update[] = " userdashboards.$key = userdashboards.$key + 1 ";
								}
							}
							else
							{
								if(is_null($value))
								{
									$update[] = " userdashboards.$key = 0 ";
								}
								elseif($value > 0)
								{
									$update[] = " userdashboards.$key = userdashboards.$key - 1 ";
								}
							}
						}
					}
					break;
			}
		}

		if(empty($update))
		{
			return;
		}

		$update = implode(",", $update);
		$query  = "UPDATE userdashboards SET $update WHERE user_id = $_user_id LIMIT 1 ";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * plus the field of userdashboards
	 *
	 * @param      <type>  $_user_id  The poll identifier
	 * @param      <type>  $_field    The field
	 */
	public static function plus($_user_id, $_field, $_plus = 1, $_options = [])
	{
		$default_options = ['type' => 'plus'];
		if(!is_array($_options))
		{
			$_options = [];
		}
		$_options = array_merge($default_options, $_options);
		return self::change_dashboard($_user_id, $_field, $_plus, $_options);
	}


	/**
	 * minus the poll userdashboards
	 *
	 * @param      <type>   $_user_id  The poll identifier
	 * @param      <type>   $_field    The field
	 * @param      integer  $_minus    The minus
	 * @param      array    $_options  The options
	 *
	 * @return     <type>   ( description_of_the_return_value )
	 */
	public static function minus($_user_id, $_field, $_minus = 1, $_options = [])
	{
		$default_options = ['type' => 'minus'];
		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);
		return self::change_dashboard($_user_id, $_field, $_minus, $_options);
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
			"get_count"      => false,
			// enable|disable paignation,
			"pagenation"     => true,
			// for example in get_count mode we needless to limit and pagenation
			// default limit of record is 15
			// set the limit = null and pagenation = false to get all record whitout limit
			"limit"          => 15,
			// for manual pagenation set the statrt_limit and end limit
			"start_limit"    => 0,
			// for manual pagenation set the statrt_limit and end limit
			"end_limit"      => 10,
			// the the last record inserted to post table
			"get_last"       => false,
			// default order by DESC you can change to DESC
			"order"          => "DESC",
			// custom sort by field
			"sort"           => null,
			// search by user
			"user"           => null,
			// search by mobile
			"mobile"         => null,
			// search by date
			"date"           => null,
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
			$public_fields  =
			" COUNT(*) AS 'usercount' FROM
				users
			FROM
				users
			LEFT JOIN userdashboards ON userdashboards.user_id = users.id";
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


		if(isset($_options['mobile']) && $_options['mobile'])
		{
			$where[] = " users.user_mobile LIKE '%$_options[mobile]%' ";
		}

		if(isset($_options['user']) && $_options['user'])
		{
			$where[] = " users.id = $_options[user] ";
		}

		if(isset($_options['date']) && $_options['date'])
		{
			if(mb_strlen($_options['date']) === 8)
			{
				$where[] = " DATE(users.user_createdate) = DATE('$_options[date]') ";
			}
			else
			{
				$where[] = " TIME(users.user_createdate) = TIME('$_options[date]') ";
			}
		}


		// ------------------ get last
		$order = null;
		if($_options['get_last'])
		{
			if($_options['sort'])
			{
				$order = " ORDER BY $_options[sort] $_options[order] ";
			}
			else
			{
				$order = " ORDER BY users.id DESC ";
			}
		}
		else
		{
			if($_options['sort'])
			{
				$order = " ORDER BY $_options[sort] $_options[order] ";
			}
			else
			{
				$order = " ORDER BY users.id $_options[order] ";
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
		unset($_options['caller']);
		unset($_options['user']);
		unset($_options['date']);
		unset($_options['mobile']);

		foreach ($_options as $key => $value)
		{
			if(is_array($value))
			{
				if(isset($value[0]) && isset($value[1]) && is_string($value[0]) && is_string($value[1]))
				{
					// for similar "users.`field` LIKE '%valud%'"
					$where[] = " users.`$key` $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " users.`$key` IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " users.`$key` = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " users.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");
		$search = null;
		if($_string != null)
		{
			$_string = trim($_string);
			$search =
			"(
				users.user_displayname 	LIKE '%$_string%' OR
				users.user_mobile 		LIKE '%$_string%'

			)";
			if($where)
			{
				$search = " AND ". $search;
			}
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
			$pagenation_query =
			"SELECT
				COUNT(users.id) AS `count`
			FROM
				users
			LEFT JOIN userdashboards ON userdashboards.user_id = users.id
			$where $search ";
			$pagenation_query = \lib\db::get($pagenation_query, 'count', true);

			list($limit_start, $limit) = \lib\db::pagnation((int) $pagenation_query, $limit);
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
		$query = " SELECT $public_fields $where $search $order $limit -- users::search() -- $json";

		if(!$only_one_value)
		{
			$result = \lib\db::get($query);
			$result = \lib\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \lib\db::get($query, 'usercount', true);
		}

		return $result;
	}
}
?>