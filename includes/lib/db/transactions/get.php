<?php
namespace lib\db\transactions;
use \lib\db;
use \lib\utility;


trait get
{
	public static $fields =
	"
		transactions.title              		AS `title`,
		-- transactions.transactionitem_id 		AS `transactionitem_id`,
		-- transactions.user_id            		AS `user_id`,
		transactions.type               		AS `type`,
		units.title            					AS `unit`,
		transactions.plus               		AS `plus`,
		transactions.minus              		AS `minus`,
		transactions.budgetbefore       		AS `budgetbefore`,
		transactions.budget             		AS `budget`,
		-- transactions.exchange_id        		AS `exchange_id`,
		-- transactions.status             		AS `status`,
		-- transactions.meta               		AS `meta`,
		-- transactions.desc               		AS `desc`,
		-- transactions.related_user_id    		AS `related_user_id`,
		-- transactions.parent_id          		AS `parent_id`,
		-- transactions.finished           		AS `finished`
		transactions.createdate         		AS `date`
		FROM
			transactions
		INNER JOIN units ON units.id = transactions.unit_id
	";

/**
	 * get log
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
			// just return the count record
			"get_count"      => false,
			// enable|disable paignation,
			"pagenation"     => true,
			// for example in get_count mode we needless to limit and pagenation
			// default limit of record is 15
			// set the limit = null and pagenation = false to get all record whitout limit
			"limit"          => 10,
			// for manual pagenation set the statrt_limit and end limit
			"start_limit"    => 0,
			// for manual pagenation set the statrt_limit and end limit
			"end_limit"      => 10,
			// the the last record inserted to post table
			"get_last"       => false,
			// default order by ASC you can change to DESC
			"order"          => "ASC",
			// custom sort by field
			"sort"           => null,
			"unit"           => null,
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
			$public_fields  = " COUNT(transactions.id) AS 'logcount' FROM transactions ";
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

		if(isset($_options['unit']) && $_options['unit'])
		{
			$_options['unit_id'] = \lib\db\units::get_id($_options['unit']);
		}

		// ------------------ get last
		$order = null;
		if($_options['get_last'])
		{
			$order = " ORDER BY transactions.id DESC ";
		}
		else
		{
			$order = " ORDER BY transactions.id $_options[order] ";
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
		unset($_options['unit']);


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
			$search =
			"(
				transactions.title 				LIKE '%$_string%'
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
			$pagenation_query = "SELECT	$public_fields $where $search ";
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
		"
			SELECT SQL_CALC_FOUND_ROWS
				$public_fields
				$where
				$search
			$order
			$limit
			-- transactions::search()
			-- $json
		";

		if(!$only_one_value)
		{
			$result = \lib\db::get($query);
			$result = \lib\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \lib\db::get($query, 'logcount', true);
		}

		return $result;
	}
}
?>