<?php
namespace content_election\lib;

class candidas
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
			\lib\db::query("INSERT INTO candidas SET $set", 'election');
			return \lib\db::insert_id(\lib\db::$link_open['election']);
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
			$query = "SELECT * FROM candidas WHERE id = $_id LIMIT 1";
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

		$query = "UPDATE candidas SET $set WHERE id = $_id LIMIT 1";
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
			"limit"          => 300,
			// for manual pagenation set the statrt_limit and end limit
			"start_limit"    => 0,
			// for manual pagenation set the statrt_limit and end limit
			"end_limit"      => 100,
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
			$public_fields  = " COUNT(candidas.id) AS 'electioncount' FROM	candidas";
			$limit          = null;
			$only_one_value = true;
		}
		else
		{
			$limit         = null;
			$public_fields = " candidas.*, elections.title FROM candidas INNER JOIN elections ON elections.id = candidas.election_id";

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
				$order = " ORDER BY candidas.id DESC ";
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
				$order = " ORDER BY candidas.id $_options[order] ";
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
					// for similar "candidas.`field` LIKE '%valud%'"
					$where[] = " candidas.`$key` $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " candidas.`$key` IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " candidas.`$key` = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " candidas.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");
		$search = null;
		if($_string != null)
		{
			$_string = trim($_string);

			$search = "(candidas.title  LIKE '%$_string%' )";
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
			$pagenation_query = "SELECT	COUNT(candidas.id) AS `count`	FROM candidas	$where $search ";
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
		$query = " SELECT $public_fields $where $search $order $limit -- candidas::search() 	-- $json";

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
	 * { item_description }
	 */
	public static function list($_election_id)
	{
		if($_election_id && is_numeric($_election_id))
		{
			$query = "SELECT candidas.*, CONCAT(name, ' ', family) as `name_family` FROM candidas WHERE candidas.election_id = $_election_id AND candidas.status = 'active' ";
			$result = \lib\db::get($query, null, false, 'election');

			return $result;
		}
	}


	public static function get_list_all($_cat)
	{

		$query =
		"
			SELECT
				candidas.*,
				(
					SELECT ((results.total * 100) / elections.voted)
					FROM results
					WHERE results.election_id = elections.id
					AND results.candida_id = candidas.id
					AND results.status = 'enable'
					LIMIT 1
				) AS `win_present`,
				((elections.voted * 100) / elections.eligible) AS `work_present`,
				(
					SELECT ((results.total * 100) / elections.eligible)
					FROM results
					WHERE results.election_id = elections.id
					AND results.candida_id = candidas.id
					AND results.status = 'enable'
					LIMIT 1
				) AS `win_present_all`
			FROM
				candidas
			INNER JOIN elections ON elections.id = candidas.election_id
			WHERE
				elections.cat = '$_cat' AND
				candidas.status = 'active'
			";
		$result = \lib\db::get($query, null, false, 'election');
		// var_dump($result);exit();
		return $result;

	}

}
?>