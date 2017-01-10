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
			// default limit of record is 10
			// set the limit  = null and pagenation = false to get all record whitout limit
			"limit"           => 10,

			// for manual pagenation set the statrt_limit and end limit
			"start_limit"     => 0,

			// for manual pagenation set the statrt_limit and end limit
			"end_limit"       => 10,

			// get the login id to load favourites post INNER JOIN options by this id
			"login"           => false,

			// the the last record inserted to post table
			"get_last"        => false,

			// disable check 'publish' poll because this is my poll
			// if my_poll === true then
			// option all === true to search in all post
			// option check_language = false to not check langupage
			"my_poll"         => false,

			// no thing yet.
			"admin"           => false,

			// default order by ASC you can change to DESC
			"order"           => "ASC",

			// if all == false load just sarshomar poll
			// if all == true disable check sarshomar poll to load
			"all"             => false,

			// default we not search in news (posts.post_type = 'post')
			// the news type is 'post'
			// set the 'post' => true to search in news and polls
			"search_post"     => false,

			// default we check the language of user
			// and load only the post was this language or her lang is null
			"check_language"  => true,

		];
		$_options = array_merge($default_options, $_options);

		// ------------------ favourites
		$favourites = null;
		if($_options['login'])
		{
			$favourites =
			"
				LEFT JOIN options
					ON options.post_id = posts.id AND
						options.option_key = 'favourites' AND
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
			$public_fields  = " COUNT(posts.id) AS 'postcount' FROM posts ";
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
			$order = " ORDER BY posts.id DESC ";
		}
		else
		{
			$order = " ORDER BY posts.id $_options[order] ";
		}

		// if in my poll return publish and public poll
		if($_options['my_poll'] === false)
		{
			$where[] = " posts.post_status = 'publish' ";
			$where[] = " posts.post_privacy = 'public' ";
		}
		elseif($_options['my_poll'] === true)
		{
			$_options['all']            = true;
			$_options['check_language'] = false;
		}

		// if all == true return all type of polls, sarshomar or personal
		if($_options['all'] === false)
		{
			$where[] = " posts.post_sarshomar = 1 ";
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];

		// default we not search in news of service
		if($_options['search_post'] === false)
		{
			$where[] = " posts.post_type IN ('poll','survey') ";
		}

		if($_options['check_language'] === true)
		{
			$language = \lib\define::get_language();
			$where[] = " (posts.post_language IS NULL OR posts.post_language = '$language') ";
		}

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
		unset($_options['all']);
		unset($_options['search_post']);
		unset($_options['check_language']);

		foreach ($_options as $key => $value)
		{
			if(is_array($value))
			{
				// for similar "posts.`field` LIKE '%valud%'"
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
		// ------------------ favourites
		if($favourites)
		{
			$public_fields = " options.option_value AS 'favourites', ". $public_fields;
		}

		$query =
		"
			SELECT
				$public_fields
			$favourites
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