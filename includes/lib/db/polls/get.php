<?php
namespace lib\db\polls;

trait get
{

	/**
	 * Gets the count of poll by search
	 *
	 * @param      array   $_args  The arguments
	 *
	 * @return     <type>  The count.
	 */
	public static function get_count($_search = null, $_args = [])
	{
		$_args['get_count']  = true;
		$_args['pagenation'] = false;
		$result = self::search($_search, $_args);
		return $result;
	}


	/**
	 * get list of polls
	 * @param  [type] $_user_id set userid
	 * @param  [type] $_return  set return field value
	 * @param  string $_type    set type of poll
	 * @return [type]           an array or number
	 */
	public static function get($_user_id = null, $_return = null, $_type = null)
	{
		// calc type if needed
		if($_type === null)
		{
			$_type = "posts.post_type LIKE 'poll\_%'";
		}
		else
		{
			$_type = "posts.post_type = 'poll_". $_type. "'";
		}
		// calc user id if exist
		if($_user_id)
		{
			$_user_id = "AND posts.user_id = $_user_id";
		}
		else
		{
			$_user_id = null;
		}
		// generate query string
		$qry = "SELECT * FROM posts WHERE $_type $_user_id";
		// run query
		if($_return && $_return !== 'count')
		{
			$result = \lib\db::get($qry, $_return);
		}
		else
		{
			$result = \lib\db::get($qry);
		}
		// if user want count of result return count of it
		if($_return === 'count')
		{
			return count($result);
		}
		// return last insert id
		return $result;
	}


	/**
	 * get title and meta of poll
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  The poll.
	 */
	public static function get_poll($_poll_id)
	{
		if(!is_int($_poll_id) && !is_string($_poll_id))
		{
			return false;
		}

		if(!isset(self::$_POLL[$_poll_id]))
		{
			$public_fields = self::$fields;
			$query =
			"
				SELECT
					$public_fields
				WHERE
					posts.id = $_poll_id
				LIMIT 1
				-- polls::get_poll()
			";
			$result = \lib\db::get($query, null);
			$result = \lib\utility\filter::meta_decode($result);
			if(isset($result[0]))
			{
				self::$_POLL[$_poll_id] = $result[0];
			}
			else
			{
				self::$_POLL[$_poll_id] = $result;
			}
		}
		return self::$_POLL[$_poll_id];
	}


	/**
	 * Gets the poll status.
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  The poll status.
	 */
	public static function get_poll_status($_poll_id)
	{
		$poll = self::get_poll($_poll_id);
		if(isset($poll['status']))
		{
			return $poll['status'];
		}
		return null;

		$query  = "SELECT post_status AS `status` FROM posts WHERE posts.id = $_poll_id LIMIT 1";
		$result = \lib\db::get($query, 'status', true);
		if(!is_string($result))
		{
			$result = null;
		}
		return $result;
	}


	/**
	 * Gets the poll url.
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  The poll url.
	 */
	public static function get_poll_url($_poll_id)
	{
		$poll = self::get_poll($_poll_id);
		if(isset($poll['url']))
		{
			return $poll['url'];
		}
		return null;

		$query  = "SELECT post_url AS `url` FROM posts WHERE posts.id = $_poll_id LIMIT 1";
		$result = \lib\db::get($query, 'url', true);
		if(!is_string($result))
		{
			$result = null;
		}
		return $result;
	}


	/**
	 * get title of polls
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  The poll title.
	 */
	public static function get_poll_title($_poll_id)
	{
		$poll = self::get_poll($_poll_id);
		if(isset($poll['title']))
		{
			return $poll['title'];
		}
		return null;

		$query  = "SELECT post_title AS `title` FROM posts WHERE posts.id = $_poll_id LIMIT 1";
		$result = \lib\db::get($query, 'title', true);
		if(!is_string($result))
		{
			$result = null;
		}
		return $result;
	}


	/**
	 * get meta of polls
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  The poll title.
	 */
	public static function get_poll_meta($_poll_id)
	{
		$poll = self::get_poll($_poll_id);
		if(isset($poll['meta']))
		{
			return $poll['meta'];
		}
		return null;
		$query  = "SELECT post_meta AS `meta` FROM posts WHERE posts.id = $_poll_id LIMIT 1";
		$result = \lib\db::get($query, 'meta', true);
		if(is_string($result))
		{
			$result = json_decode($result, true);
		}

		if(!is_array($result))
		{
			$result = [];
		}
		return $result;
	}


	/**
	 * Gets the last url.
	 * check last question to answere user and return url of this poll
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function get_next_url($_user_id)
	{
		$result = self::get_last($_user_id);
		if(isset($result['url']))
		{
			return $result['url'];
		}
		else
		{
			return null;
		}
	}


	/**
	 * get previous poll the users answer it
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function get_previous_url($_user_id, $_corrent_post_id)
	{
		$query =
		"
			SELECT
				posts.post_url AS 'url'
			FROM
				polldetails
			INNER JOIN posts ON posts.id = polldetails.post_id
			WHERE
				polldetails.id =
				(
					SELECT
						MAX(polldetails.id)
					FROM
						polldetails
					WHERE
						polldetails.id <
							(
								SELECT
									MAX(polldetails.id)
								FROM
									polldetails
								WHERE
									polldetails.user_id = $_user_id AND
									polldetails.post_id = $_corrent_post_id
							)
				)
			LIMIT 1
			-- polls::get_previous_url()
			-- to get previous of answered this user
		";
		$result= \lib\db::get($query, 'url', true);
		return $result;
		return self::get_poll_url($result);
	}


	/**
	 * get the random record of sarshomar knowledge
	 *
	 * @return     <type>  The random.
	 */
	public static function get_random()
	{
		$language = \lib\define::get_language();
		$public_fields = self::$fields;
		$query =
		"
			SELECT
				$public_fields
			WHERE
				(
					posts.post_language IS NULL OR
					posts.post_language = '$language'
				) AND
				posts.post_sarshomar = 1 AND
				posts.post_status = 'publish'
			ORDER BY RAND()
			LIMIT 1
		";
		$result =\lib\db::get($query);
		if($result && isset($result[0]))
		{
			$result = \lib\utility\filter::meta_decode($result);
			$result = $result[0];
		}
		return $result;
	}


	/**
	 * get full post data
	 *
	 * @param      string  $_poll_id  The poll identifier
	 *
	 * @return     <type>  The full.
	 */
	public static function get_full($_poll_id)
	{

		if(!$_poll_id)
		{
			return false;
		}
		$query =
		"
			SELECT
					posts.*,
					options.*,
					pollstats.*,
					pollopts.*,
					ranks.*
			FROM
				posts
			LEFT JOIN pollstats	ON pollstats.post_id = posts.id
			LEFT JOIN options	ON options.post_id  = posts.id AND	options.user_id IS NULL
			LEFT JOIN pollopts	ON pollopts.post_id  = posts.id
			LEFT JOIN ranks		ON ranks.post_id     = posts.id
			WHERE posts.id = $_poll_id
		";

		$result = \lib\db::get($query);
		$result = \lib\utility\filter::meta_decode($result);
		return $result;
	}


	/**
	 * Counts the number of poll status.
	 *
	 * @return     <type>  Number of poll status.
	 */
	public static function count_poll_status()
	{
		$query =
		"
			SELECT
				COUNT(posts.id) AS `count`,
				posts.post_status AS `status`
			FROM
				posts
			WHERE posts.post_type IN ('poll', 'survey')
			GROUP BY
				status
			";
		$result = \lib\db::get($query, ['status', 'count']);
		return $result;
	}


}
?>