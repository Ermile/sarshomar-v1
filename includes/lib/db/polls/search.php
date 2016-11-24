<?php
namespace lib\db\polls;

trait search
{

	/**
	 * search in posts
	 *
	 * @param      <type>  $_string   The string
	 * @param      array   $_options  The options
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function search($_string = null, $_options = [])
	{

		$where = [];

		if(!$_string && empty($_options))
		{
			return null;
		}

		$default_options =
		[
			"get_count"   => false,
			"pagenation"  => true,
			"limit"       => 10,
			"start_limit" => 0,
			"end_limit"   => 10,
			"login"       => false,
			"get_last"    => false,
			"my_poll"     => false,
			"admin"       => false,
			"order"		  => "ASC",
		];

		$_options = array_merge($default_options, $_options);
		// ------------------ faivorites
		$faivorites = null;
		if($_options['login'])
		{
			$faivorites =
			"
				LEFT JOIN options
					ON options.post_id = posts.id AND
						options.option_key = 'faivorites' AND
						options.user_id = $_options[login] AND
						options.option_status = 'enable' ";
		}

		// ------------------ pagenation
		$pagenation = false;
		if($_options['pagenation'])
		{
			// page nation
			$pagenation = true;
		}

		// ------------------ get count
		$only_one_value = false;
		if($_options['get_count'] === true)
		{
			$public_fields = " COUNT(posts.id) AS 'postcount' FROM posts ";
			$limit = null;
			$only_one_value = true;
		}
		else
		{
			$public_fields = self::$fields;
			$limit = $_options['limit'];
		}

		// ------------------ get last
		$order = null;
		if($_options['get_last'])
		{
			$order = " ORDER BY posts.id DESC ";
		}
		else
		{
			$order = " ORDER BY posts.id $_options[order] ";
		}


		// if in my poll retur all poll
		if($_options['my_poll'] === false)
		{
			$where[] = " posts.post_status = 'publish' ";
			$where[] = " posts.post_sarshomar = 1 ";
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];


		// ------------------ remove system index
		// unset some value to not search in database as a field
		unset($_options['pagenation']);
		unset($_options['get_count']);
		unset($_options['limit']);
		unset($_options['login']);
		unset($_options['get_last']);
		unset($_options['my_poll']);
		unset($_options['admin']);
		unset($_options['start_limit']);
		unset($_options['end_limit']);
		unset($_options['order']);

		$where[] = " posts.post_type != 'post' ";

		foreach ($_options as $key => $value)
		{
			if(is_array($value))
			{
				$where[] = " posts.`$key` $value[0] $value[1] ";
			}
			else
			{
				$where[] = " posts.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");

		$search = null;
		if($_string != null)
		{
			$_string = \lib\utility\safe::safe($_string);

			$search =
			"(
				posts.post_title 	LIKE '%$_string%' OR
				posts.post_content 	LIKE '%$_string%' OR
				posts.post_url 		LIKE '%$_string%' OR
				posts.post_meta 	LIKE '%$_string%'
			)";
			if($where)
			{
				$search = " AND ". $search;
			}
		}

		if($pagenation)
		{
			$pagenation_query = "SELECT	$public_fields	WHERE $where $search ";
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
		// ------------------ faivorites
		if($faivorites)
		{
			$public_fields = " options.option_value AS 'faivorites', ". $public_fields;
		}

		$query =
		"
			SELECT
				$public_fields
			$faivorites
			WHERE
				$where
				$search
			$order
			$limit
			-- polls::search()
		";

		if(!$only_one_value)
		{
			$result = \lib\db::get($query);
			$result = \lib\utility\filter::meta_decode($result);
		}
		else
		{
			$result = \lib\db::get($query, 'postcount', true);
		}
		return $result;
	}
}
?>