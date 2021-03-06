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

			// get the login id to load fav post INNER JOIN options by this id
			"login"           => false,

			// the the last record inserted to post table
			"get_last"        => false,

			// disable check 'publish' poll because this is my poll
			// if my_poll === true then
			// option all === true to search in all post
			// option check_language = false to not check langupage
			"in"              => 'sarshomar',

			// no thing yet.
			"admin"           => false,

			// default order by ASC you can change to DESC
			"order"           => "ASC",

			// custom sort by field
			"sort"			  => null,

			// if all == false load just sarshomar poll
			// if all == true disable check sarshomar poll to load
			"all"             => false,

			// default we not search in news (posts.post_type = 'post')
			// the news type is 'post'
			// set the 'post' => true to search in news and polls
			"search_post"     => false,

			// default we check the language of user
			// and load only the post was this language or her lang is null
			"check_language" => true,
			'api_mode'       => false,

		];
		$_options = array_merge($default_options, $_options);

		// ------------------ fav
		$my_fav = null;
		$my_like       = null;
		$is_answered     = null;

		if($_options['login'])
		{
			$my_fav =
			"
				LEFT JOIN options AS `fav`
					ON fav.post_id = posts.id AND
						fav.option_key = 'fav' AND
						fav.user_id = $_options[login] AND
						fav.option_status = 'enable'
			";

			$my_like =
			"
				LEFT JOIN options AS `my_like`
					ON my_like.post_id = posts.id AND
						my_like.option_key = 'like' AND
						my_like.user_id = $_options[login] AND
						my_like.option_status = 'enable'
			";

			$is_answered =
			"
				IF((
				SELECT answers.id
				FROM answers
				WHERE
					answers.post_id = posts.id AND
					answers.user_id = $_options[login]
				) IS NOT NULL, TRUE, FALSE) AS `is_answered`,
			";

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
		$get_count      = false;

		if($_options['get_count'] === true)
		{
			$get_count      = true;
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
			if($_options['sort'])
			{
				$order = " ORDER BY $_options[sort] $_options[order] ";
			}
			else
			{
				$order = " ORDER BY posts.id DESC ";
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
				$order = " ORDER BY posts.id $_options[order] ";
			}
		}


		if(is_string($_options['in']))
		{
			// search in sarshomar
			switch ($_options['in'])
			{
				// just admin can set this method
				case 'people':
					$_options['post_sarshomar'] = 0 ;
					break;

				case 'me':
					$_options['user_id'] = $_options['login'];
					break;

				case 'article':
					$_options['post_status']  = 'publish';
					$_options['post_privacy'] = 'public';
					$_options['post_type']    = 'article';
					break;

				case 'all':
					if(!$_options['admin'])
					{
						$_options['post_privacy'] = 'public';
						$_options['post_status']  = 'publish';
					}
					break;
				case 'sarshomar':
				case null:
				default:
					$_options['post_status']    = 'publish';
					$_options['post_privacy']   = 'public';
					$_options['post_sarshomar'] = 1 ;
					break;
			}
		}
		elseif(is_array($_options['in']))
		{
			if(in_array('me', $_options['in']) && in_array('sarshomar', $_options['in']))
			{
				$where[] =
				"
				 (
				 	(
						posts.post_sarshomar = 1 AND
						posts.post_status    = 'publish'
				 	)
				 	OR
				 	(
				 		posts.user_id = $_options[login] AND
				 		posts.post_status = 'publish'
				 	)
				 )
				 ";
			}
		}
		else
		{
			// no thing!
		}

		$start_limit = $_options['start_limit'];
		$end_limit   = $_options['end_limit'];

		// default we not search in news of service
		if($_options['search_post'] === false)
		{
			if(!isset($_options['post_type']))
			{
				$where[] = " posts.post_type IN ('poll','survey') ";
			}
		}

		if($_options['check_language'] === true)
		{
			if(!isset($_options['post_language']))
			{
				$language = \lib\define::get_language();
				$where[]  = " (posts.post_language IS NULL OR posts.post_language = '$language') ";
			}
		}

		$api_mode = false;
		if($_options['api_mode'] === true)
		{
			$api_mode = true;
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
		unset($_options['sort']);
		unset($_options['api_mode']);
		unset($_options['in']);

		foreach ($_options as $key => $value)
		{
			if(is_array($value))
			{
				if(isset($value[0]) && isset($value[1]) && is_string($value[0]) && is_string($value[1]))
				{
					// for similar "posts.`field` LIKE '%valud%'"
					$where[] = " posts.`$key` $value[0] $value[1] ";
				}
			}
			elseif($value === null)
			{
				$where[] = " posts.`$key` IS NULL ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " posts.`$key` = $value ";
			}
			elseif(is_string($value))
			{
				$where[] = " posts.`$key` = '$value' ";
			}
		}

		$where = join($where, " AND ");

		$search = null;
		if($_string != null)
		{
			$_string = trim($_string);
			$search =
			"(
				posts.post_title 	LIKE '%$_string%' OR
				posts.post_url 		LIKE '%$_string%'
			)";
			if($where)
			{
				$search = " AND ". $search;
			}
		}
		if($api_mode)
		{
			$limit = " LIMIT $start_limit, $limit ";
		}
		elseif($pagenation && !$get_count)
		{
			$pagenation_query = "SELECT	COUNT(*) AS `count` FROM posts LEFT JOIN ranks ON ranks.post_id = posts.id	WHERE $where $search ";
			$pagenation_query = (int) \lib\db::get($pagenation_query, 'count', true);
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
		if(!$get_count)
		{
			// ------------------ fav
			if($my_fav)
			{
				$public_fields = " fav.option_value AS 'my_fav', ". $public_fields;
			}

			if($my_like)
			{
				$public_fields = " my_like.option_value AS 'my_like', ". $public_fields;
			}

			if($is_answered)
			{
				$public_fields = $is_answered. $public_fields;
			}
		}
		$json = json_encode(func_get_args());
		$query =
		"
			SELECT SQL_CALC_FOUND_ROWS
				$public_fields
			$my_fav
			$my_like

			WHERE
				$where
				$search
			$order
			$limit
			-- polls::search()
			-- $json
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

		if($api_mode)
		{
			$found_rows = \lib\db::get("SELECT FOUND_ROWS() AS `total`", 'total', true);
			\lib\storage::set_total_record($found_rows);
		}
		return $result;
	}
}
?>