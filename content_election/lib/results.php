<?php
namespace content_election\lib;

class results
{

	/**
	 * add new election
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert($_args)
	{
		$set = \lib\db\config::make_set($_args);
		if($set)
		{
			\lib\db::query("INSERT INTO results SET $set", 'election');
			return \lib\db::insert_id();
		}
	}


	/**
	 * insert multi record in results
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function insert_multi($_args)
	{
		$_args = \lib\db\config::make_multi_insert($_args);
		if($_args)
		{
			return \lib\db::query("INSERT INTO results $_args", 'election');
		}
	}

	/**
	 * get election record
	 *
	 * @param      <type>  $_id    The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_id)
	{
		if($_id && is_numeric($_id))
		{
			$query = "SELECT * FROM results WHERE id = $_id LIMIT 1";
			$result = \lib\db::get($query, null, true, 'election');
			return $result;
		}
		return false;
	}


	/**
	 * update election
	 *
	 * @param      <type>  $_args  The arguments
	 * @param      <type>  $_id    The identifier
	 */
	public static function update($_args, $_id)
	{
		$set = \lib\db\config::make_set($_args);
		if(!$set || !$_id || !is_numeric($_id))
		{
			return false;
		}

		$query = "UPDATE results SET $set WHERE id = $_id LIMIT 1";
		return \lib\db::query($query, 'election');
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
			// default return of this function 10 last record of election
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
			"
				COUNT(results.id) AS 'electioncount'
				FROM
					results
					INNER JOIN candidas ON candidas.id = results.candida_id
					LEFT JOIN elections ON elections.id = results.election_id
					LEFT JOIN reports ON reports.id = results.report_id
			";
			$limit          = null;
			$only_one_value = true;
		}
		else
		{
			$limit         = null;
			$public_fields =
			"
					results.*,
					elections.title,
					candidas.*,
					reports.*
				FROM
					results
				INNER JOIN candidas ON candidas.id = results.candida_id
				LEFT JOIN elections ON elections.id = results.election_id
				LEFT JOIN reports ON reports.id = results.report_id
			";

			if($_options['limit'])
			{
				$limit = $_options['limit'];
			}
		}


		if($_options['sort'])
		{
			$temp_sort = null;
			switch ($_options['sort'])
			{
				default:
					$temp_sort = $_options['sort'];
					break;
			}
			$_options['sort'] = $temp_sort;
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
				$order = " ORDER BY results.id DESC ";
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
				$order = " ORDER BY results.id $_options[order] ";
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
					// for similar "results.`field` LIKE '%valud%'"
					$where[] = " results.`$key` $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " results.`$key` IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " results.`$key` = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " results.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");
		$search = null;
		if($_string != null)
		{
			$_string = trim($_string);

			$search = "(results.title  LIKE '%$_string%' )";
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
			$pagenation_query = "SELECT	COUNT(results.id) AS `count`	FROM results	$where $search ";
			$pagenation_query = \lib\db::get($pagenation_query, 'count', true, 'election');

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
		$query = " SELECT $public_fields $where $search $order $limit -- results::search() 	-- $json";

		if(!$only_one_value)
		{
			$result = \lib\db::get($query, null, false, 'election');
			$result = \lib\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \lib\db::get($query, 'electioncount', true, 'election');
		}

		return $result;
	}


	/**
	 * Gets the total.
	 *
	 * @param      <type>  $_election_id  The election identifier
	 */
	public static function get_total($_election_id)
	{
		$query =
		"
			SELECT
				SUM(results.total) AS `total`,
				candidas.name,
				candidas.family
			FROM
				results
			INNER JOIN candidas ON candidas.id = results.candida_id
			INNER JOIN elections ON elections.id = results.election_id
			WHERE
				elections.id   = $_election_id AND
				results.status = 'enable'
			GROUP BY candidas.name, candidas.family
			ORDER BY total DESC
		";
		$result = \lib\db::get($query, null, false, 'election');
		return $result;
	}

}
?>