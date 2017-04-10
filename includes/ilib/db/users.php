<?php
namespace ilib\db;

class users extends \lib\db\users
{
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
	 * Gets the by username.
	 *
	 * @param      <type>  $_username  The username
	 *
	 * @return     <type>  The by username.
	 */
	public static function get_by_username($_username)
	{
		$query = "SELECT * FROM users WHERE user_username  = '$_username' LIMIT 1 ";
		return \lib\db::get($query, null, true);
	}


	/**
	 * Gets the count.
	 *
	 * @param      <type>  $_type  The type
	 *
	 * @return     <type>  The count.
	 */
	public static function get_count($_type = null)
	{
		switch ($_type)
		{
			case 'all':
				$query = "SELECT COUNT(users.id) AS `count` FROM users WHERE user_port  != 'site_guest' ";
				return \lib\db::get($query, 'count', true);
				break;

			case 'available':
				$query = "SELECT COUNT(users.id) AS `count` FROM users WHERE user_port NOT LIKE '%guest%' ";
				return \lib\db::get($query, 'count', true);
				break;

			case 'port':
				$query = "SELECT COUNT(users.id) AS `count`, `user_port` AS `port` FROM users GROUP BY user_port ";
				$result =  \lib\db::get($query, ['port', 'count']);
				$temp = [];
				foreach ($result as $key => $value)
				{
					if(!$key)
					{
						$temp['unknown'] = (int) $value;
					}
					else
					{
						$temp[$key] = (int) $value;
					}
				}
				return $temp;
				break;
			default:
				return parent::get_count($_type);
				break;
		}
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