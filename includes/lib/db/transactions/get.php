<?php
namespace lib\db\transactions;
use \lib\db;
use \lib\utility;


trait get
{
	public static $fields =
	"
		transactions.id             			AS `id`,
		transactions.title              		AS `title`,
		transactions.type               		AS `type`,
		units.title            					AS `unit`,
		transactions.plus               		AS `plus`,
		transactions.minus              		AS `minus`,
		transactions.budgetbefore       		AS `budgetbefore`,
		transactions.budget             		AS `budget`,
		transactions.createdate         		AS `date`
		FROM
			transactions
		INNER JOIN transactionitems ON transactionitems.id = transactions.transactionitem_id
		INNER JOIN units ON units.id = transactions.unit_id
		INNER JOIN users ON users.id = transactions.user_id
	";

	public static $admin_fields =
	"
		transactions.id             			AS `id`,
		transactions.title              		AS `title`,
		transactions.transactionitem_id 		AS `transactionitem_id`,
		transactions.user_id            		AS `user_id`,
		transactions.type               		AS `type`,
		units.title            					AS `unit`,
		transactions.plus               		AS `plus`,
		transactions.minus              		AS `minus`,
		transactions.budgetbefore       		AS `budgetbefore`,
		transactions.budget             		AS `budget`,
		transactions.status             		AS `status`,
		transactions.meta               		AS `meta`,
		transactions.desc               		AS `desc`,
		transactions.related_user_id    		AS `related_user_id`,
		transactions.parent_id          		AS `parent_id`,
		transactions.finished           		AS `finished`,
		transactions.createdate         		AS `date`,
		users.user_mobile 	            		AS `mobile`,
		users.user_displayname            		AS `displayname`,
		transactionitems.caller            		AS `caller`

		FROM
			transactions
		INNER JOIN transactionitems ON transactionitems.id = transactions.transactionitem_id
		INNER JOIN units ON units.id = transactions.unit_id
		INNER JOIN users ON users.id = transactions.user_id
	";


	/**
	 * get transactions
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function get($_args)
	{
		$only_one_recort = false;

		if(empty($_args) || !is_array($_args))
		{
			return false;
		}

		if(isset($_args['limit']))
		{
			if($_args['limit'] == 1)
			{
				$only_one_recort = true;
			}

			$limit = "LIMIT ". $_args['limit'];
			unset($_args['limit']);
		}
		else
		{
			$limit = null;
		}

		$where = [];
		foreach ($_args as $key => $value)
		{
			if(preg_match("/\%/", $value))
			{
				$where[] = " transactions.$key LIKE '$value'";
			}
			elseif($value === null)
			{
				$where[] = " transactions.$key IS NULL";
			}
			elseif(is_numeric($value))
			{
				$where[] = " transactions.$key = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " transactions.$key = '$value'";
			}
		}
		$where = "WHERE ". join($where, " AND ");

		$query = " SELECT * FROM transactions $where $limit ";

		$result = \lib\db::get($query, null, $only_one_recort);
		if(isset($result['meta']) && substr($result['meta'], 0, 1) == '{')
		{
			$result['meta'] = json_decode($result['meta'], true);
		}
		return $result;
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
			"get_count"      => false,
			"pagenation"     => true,
			"limit"          => 10,
			"start_limit"    => 0,
			"end_limit"      => 10,
			"get_last"       => false,
			"order"          => "ASC",
			"sort"           => null,
			"unit"           => null,
			"type"           => null,
			"mobile"         => null,
			"admin"          => false,
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
			$public_fields  = " COUNT(transactions.id) AS 'transactionscount' FROM transactions ";
			$limit          = null;
			$only_one_value = true;
		}
		else
		{
			$limit         = null;
			if($_options['admin'])
			{
				$public_fields = self::$admin_fields;
			}
			else
			{
				$public_fields = self::$fields;
			}
			if($_options['limit'])
			{
				$limit = $_options['limit'];
			}
		}

		if(isset($_options['unit']) && $_options['unit'])
		{
			$_options['unit_id'] = \lib\db\units::get_id($_options['unit']);
		}

		if(!is_string($_options['order']) || (is_string($_options['order']) && !in_array(mb_strtolower($_options['order']), ['asc', 'desc'])))
		{
			$_options['order'] = 'ASC';
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
				$order = " ORDER BY transactions.id DESC ";
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
				$order = " ORDER BY transactions.id $_options[order] ";
			}
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];

		if(isset($_options['mobile']))
		{
			$where[] = " users.user_mobile LIKE '%$_options[mobile]%' ";
		}

		if(isset($_options['user']) && is_numeric($_options['user']))
		{
			$where[] = " users.id = $_options[user] ";
		}

		if(isset($_options['caller']))
		{
			$where[] = " transactionitems.caller = '$_options[caller]' ";
		}
		if(isset($_options['date']))
		{
			$where[] = " DATE(transactions.createdate) = DATE('$_options[date]') ";
		}

		unset($_options['pagenation']);
		unset($_options['get_count']);
		unset($_options['limit']);
		unset($_options['start_limit']);
		unset($_options['end_limit']);
		unset($_options['get_last']);
		unset($_options['order']);
		unset($_options['sort']);
		unset($_options['unit']);
		unset($_options['type']);
		unset($_options['admin']);
		unset($_options['mobile']);
		unset($_options['user']);
		unset($_options['caller']);
		unset($_options['date']);

		foreach ($_options as $key => $value)
		{
			if(is_array($value))
			{
				if(isset($value[0]) && isset($value[1]) && is_string($value[0]) && is_string($value[1]))
				{
					// for similar "transactions.`field` LIKE '%valud%'"
					$where[] = " transactions.$key $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " transactions.$key IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " transactions.$key = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " transactions.$key = '$value' ";
			}
		}

		$where = join($where, " AND ");
		$search = null;
		if($_string != null)
		{
			$_string = trim($_string);
			$search =
			"(
				transactions.title 		LIKE '%$_string%' OR
				transactionitems.caller	LIKE '%$_string%'
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
			$pagenation_query = (int) \lib\db::get(
			"SELECT COUNT(*) AS `count` FROM transactions
			INNER JOIN transactionitems ON transactionitems.id = transactions.transactionitem_id
			INNER JOIN units ON units.id = transactions.unit_id
			INNER JOIN users ON users.id = transactions.user_id $where $search ", 'count', true);
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
		$query =
		"SELECT
		$public_fields
		$where
		$search
		$order
		$limit
		-- transactions::search()
		-- $json";

		if(!$only_one_value)
		{
			$result = \lib\db::get($query);
			$result = \lib\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \lib\db::get($query, 'transactionscount', true);
		}

		return $result;
	}
}
?>