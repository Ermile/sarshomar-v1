<?php
namespace lib\db;

/** work with polls **/
class polls
{
	/**
	 * this library work with acoount
	 * v3.1
	 */

	public static $fields =
	"
			posts.id					as 'id',
			posts.post_language 		as 'language',
			posts.post_title 			as 'title',
			posts.post_slug 			as 'slug',
			posts.post_url 				as 'url',
			posts.post_content 			as 'content',
			posts.post_type 			as 'type',
			posts.post_comment 			as 'comment',
			posts.post_meta 			as 'meta',
			posts.post_count 			as 'count',
			posts.post_order 			as 'order',
			posts.post_status 			as 'status',
			posts.post_parent 			as 'parent',
			posts.post_publishdate 		as 'publishdate',
			posts.filter_id 			as 'filter_id',
			posts.date_modified  	    as 'date_modified',
			pollstats.id 		     	as 'pollstatsid',
			pollstats.total 			as 'total'
		FROM
			posts
		LEFT JOIN pollstats ON pollstats.post_id = posts.id
	";


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
	 * insert polls as post record
	 * and then insert answers of this poll into answers (options table)
	 *
	 * @param      <type>  $_args  list of polls meta and answers
	 *
	 * @return     <type>  mysql result
	 */
	public static function insert($_args)
	{
		$default_value =
		[
			'user_id'          => null,
			'post_language'    => null,
			'post_title'       => null,
			'post_slug'        => null,
			'post_url'         => time(). '_'. rand(1,20), // insert post id ofter insert record
			'post_content'     => null,
			'post_type'        => null,
			'post_status'      => null,
			'post_parent'      => null,
			'post_meta'        => null,
			'post_publishdate' => null,
			'post_gender'      => null,
			'post_survey'      => null
		];

		$_args = array_merge($default_value, $_args);

		// check user_id
		if($_args['user_id'] == null)
		{
			return false;
		}

		// check language
		if($_args['post_language'] == null)
		{
			$language = substr(\lib\router::get_storage('language'), 0, 2);
		}
		else
		{
			if(strlen($_args['post_language']) > 2)
			{
				$language = substr(\lib\router::get_storage('language'), 0, 2);
			}
			else
			{
				$language = $_args['post_language'];
			}
		}

		// check title
		if($_args['post_title'] == null)
		{
			return false;
		}

		// get slug string
		if($_args['post_slug'] == null)
		{
			$_args['post_slug'] =  \lib\utility\filter::slug($_args['post_title']);
		}

		// check type
		if($_args['post_type'] == null)
		{
			return false;
		}

		// check status
		if($_args['post_status'] == null)
		{
			$_args['post_status'] = "draft";
		}

		$result = \lib\db\posts::insert($_args);

		// new id of poll, posts.id
		$insert_id 	= \lib\db::insert_id();

		if($insert_id)
		{
			// update post url
			self::update_url($insert_id, $_args['post_title']);
			return $insert_id;
		}
		else
		{
			return false;
		}
	}


	/**
	 * update the poll url
	 * get the poll id and poll title
	 * set url = $/[encode of poll id]/[title whit replace \s to _ ]
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      <type>  $_title    The title
	 */
	public static function update_url($_poll_id, $_title, $_update = true)
	{
		$short_url = \lib\utility\shortURL::encode($_poll_id);
		// $title = preg_replace("/[\n\t\s\,\-\(\)\!\@\#\$\%\^\&\/\.\?\<\>\|\{\}\[\]\"\'\:\;\*]/", "_", $title);
		$_title = str_replace(" ", "_", $_title);
		$url = '$/' . $short_url . '/'. $_title;
		// default the update is true
		// we update the poll url
		if($_update)
		{
			return \lib\db\polls::update(['post_url' => $url], $_poll_id);
		}
		else
		{
			// update is false then we return the url
			// for update another place
			return $url;
		}

	}


	/**
	 * insert quick poll
	 * get title and answers txt then insert
	 * for telegram mode
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function insert_quick($_args)
	{
		if(!isset($_args['user_id']))
		{
			return false;
		}
		else
		{
			$user_id = $_args['user_id'];
		}

		if(!isset($_args['title']))
		{
			return false;
		}
		else
		{
			$title = $_args['title'];
		}

		$post_value =
		[
			'user_id'    => $_args['user_id'],
			'post_title' => $_args['title'],
			'post_type'  => 'select'
		];

		$insert_id = self::insert($post_value);

		if(isset($_args['answers']))
		{
			$answers = array_filter($_args['answers']);
		}
		else
		{
			$answers = null;
		}
		// check insert id and answers exist
		// for example the notify poll has no answerd
		if($insert_id && $answers){
			$answers_value = [];
			foreach ($_args['answers'] as $key => $value) {
				$answers_value[] =
				[
					'type' => 'select',
					'txt' => $value
				];
			}
			\lib\utility\answers::insert(['poll_id' => $insert_id , 'answers' => $answers_value]);
		}
		return $insert_id;
	}


	/**
	 * update polls as post record
	 *
	 * @param      <type>  $_args  The arguments
	 * @param      <type>  $_id    The identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update($_args, $_id) {
		return \lib\db\posts::update($_args, $_id);
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
	public static function get_poll($_poll_id) {
		if(!is_int($_poll_id) && !is_string($_poll_id))
		{
			return false;
		}

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
			return $result[0];
		}
		return $result;
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
		$result = self::get_poll($_poll_id);
		return isset($result['status']) ? $result['status'] : null;
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
		$result = self::get_poll($_poll_id);
		return isset($result['url']) ? $result['url'] : null;
	}


	/**
	 * get title of polls
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  The poll title.
	 */
	public static function get_poll_title($_poll_id) {
		$result = self::get_poll($_poll_id);
		return isset($result['title']) ? $result['title'] : null;
	}


	/**
	 * get meta of polls
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  The poll title.
	 */
	public static function get_poll_meta($_poll_id) {
		$result = self::get_poll($_poll_id);
		return isset($result['meta']) ? $result['meta'] : null;
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
	 * return last question for this user
	 * @param  [type] $_user_id [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function get_last($_user_id)
	{
		$public_fields = self::$fields;

		$qry ="
			SELECT
				$public_fields
			-- To get options of this poll
			LEFT JOIN options ON options.post_id = posts.id

			WHERE
				posts.post_status = 'publish' AND
				posts.post_url LIKE '$%' AND
				-- check users not answered to this poll
				posts.id NOT IN
				(
					SELECT
						polldetails.post_id
					FROM
						polldetails
					WHERE
						polldetails.user_id = $_user_id AND
						polldetails.post_id = posts.id
				)
			-- Check poll tree
			AND
				CASE
					-- If this poll not in tree  return true
					WHEN posts.post_parent IS NULL THEN TRUE
				ELSE
					-- Check this users answered to parent of this poll and her answer is important in tree
					posts.post_parent IN
					(
						SELECT
							polldetails.post_id
						FROM
							polldetails
						WHERE
							polldetails.user_id = $_user_id AND
							polldetails.post_id = posts.post_parent AND
							CONCAT('opt_', polldetails.opt) IN
								(
									SELECT
										options.option_value
									FROM
										options
									WHERE
										options.post_id = posts.id AND
										options.option_cat = 'poll_' & posts.id AND
										options.option_key = 'tree_' & posts.post_parent AND
										options.user_id IS NULL
								)
					)
				END
			ORDER BY posts.id ASC
			LIMIT 1
			-- polls::get_last()
			-- get next poll to answer user
		";
		$result = \lib\db::get($qry, null);
		$result = \lib\utility\filter::meta_decode($result);
		if(isset($result[0]))
		{
			return $result[0];
		}
		return false;
	}


	/**
	 * remove index from meta
	 *
	 * @param      <type>  $_field_meta  The field meta
	 * @param      <type>  $_poll_id     The poll identifier
	 */
	public static function remove_index_meta($_field_meta, $_poll_id)
	{

		$meta = self::get_poll_meta($_poll_id);
		if(!is_array($meta))
		{
			$meta = [];
		}

		$find_remove = false;

		foreach ($_field_meta as $key => $value) {
			if(isset($meta[$key]))
			{
				if($value === null || $value == $meta[$key])
				{
					$find_remove = true;
					unset($meta[$key]);
				}
			}
		}
		if(!$find_remove)
		{
			return true;
		}

		$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
		$meta_query = " '$meta' ";


		$query =
		"
			UPDATE
				posts
			SET
				post_meta  = $meta_query
			WHERE
				posts.id = $_poll_id
			-- polls::remove_index_meta()
			-- add new json to existing meta of post_meta
		";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * Appends a meta.
	 * meta of posts table is a json field
	 * if this field is full we merge new value and old value of this field
	 *
	 * @param      <type>  $_field_meta  The field meta
	 * @param      <type>  $_poll_id     The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function merge_meta($_field_meta, $_poll_id)
	{
		if(defined("mysql_json"))
		{
			$meta = json_encode($_field_meta, JSON_UNESCAPED_UNICODE);
			$meta_query = " JSON_MERGE(JSON_EXTRACT(posts.post_meta, '$'), '$meta')";
		}
		else
		{
			$meta = self::get_poll_meta($_poll_id);
			if(!is_array($meta))
			{
				$meta = [];
			}
			$meta = array_merge($meta, $_field_meta);

			$meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
			$meta_query = " '$meta' ";
		}

		$query =
		"
			UPDATE
				posts
			SET
				post_meta  = $meta_query
			WHERE
				posts.id = $_poll_id
			-- polls::merge_meta()
			-- add new json to existing meta of post_meta
		";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * delete answers of specefic user
	 * @param  [type] $_user_id [description]
	 * @return [type]           [description]
	 */
	public static function removeUserAnswers($_user_id)
	{
		$qry = "
			DELETE FROM
				polldetails
			WHERE
				user_id = $_user_id
			-- polls::removeUserAnswers
			";
		$result = \lib\db::query($qry);
		return $result;
	}


	/**
	 * Set bookmark of polls
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function set_bookmark($_args)
	{
		if(isset($_args['user_id']))
		{
			$user_id = $_args['user_id'];
		}
		else
		{
			return false;
		}

		if(isset($_args['poll_id']))
		{
			$poll_id = $_args['poll_id'];
		}
		else
		{
			return false;
		}

		$args =
		[
			'user_id'      => $user_id,
			'post_id'      => $poll_id,
			'option_cat'   => 'user_detail_' . $poll_id,
			'option_key'   => 'bookmark',
			'option_value' => 'like'
		];
		return \lib\db\options::insert($args);
	}


	/**
	 * delete polls
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function delete($_post_id)
	{
		if(\lib\utility\answers::delete($_post_id))
		{
			return \lib\db\posts::delete($_poll_id);
		}
		else
		{
			return false;
		}
	}


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
		if(!$_string && empty($_options))
		{
			return null;
		}

		$default_options =
		[
			"get_count"  => false,
			"pagenation" => true,
			"limit"      => 10
		];

		$_options = array_merge($default_options, $_options);

		$pagenation = false;
		if($_options['pagenation'])
		{
			// page nation
			$pagenation = true;
		}

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

		// unset some value to not search in database as a field
		unset($_options['pagenation']);
		unset($_options['get_count']);
		unset($_options['limit']);

		$where = [];
		$where[] = " posts.post_type != 'post' ";

		foreach ($_options as $key => $value) {
			$where[] = " posts.`$key` = '$value' ";
		}

		if(empty($where))
		{
			$where = null;
		}
		else
		{
			$where = join($where, " AND ");
		}

		$search = null;
		if($_string != null)
		{
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
			$limit = " LIMIT 0, $limit ";
		}

		$query =
		"
			SELECT
				$public_fields
			WHERE
				$where
				$search
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


	/**
	 * get the last poll
	 * in the knowlege page
	 *
	 * @param      array   $_args  The arguments
	 *
	 * @return     <type>  The last poll.
	 */
	public static function get_last_poll($_args = [])
	{
		$limit = 50;
		if(isset($_args['limit']))
		{
			$limit = $_args['limit'];
		}

		$public_fields = self::$fields;

		$pagenation_query = "SELECT $public_fields	WHERE posts.post_sarshomar = 1 ";
		list($limit_start, $limit) = \lib\db::pagnation($pagenation_query, $limit);
		$limit = " LIMIT $limit_start, $limit ";

		$query =
		"
			SELECT
				$public_fields
			WHERE
				posts.post_sarshomar = 1
			ORDER BY posts.id DESC
			$limit
			-- polls::get_last_poll()

		";
		$result = \lib\db::get($query);
		return \lib\utility\filter::meta_decode($result);
	}


	/**
	 * check the poll is a poll of this users of no
	 *
	 * @param      <type>   $_poll_id  The poll identifier
	 * @param      <type>   $_user_id  The user identifier
	 *
	 * @return     boolean  True if my poll, False otherwise.
	 */
	public static function is_my_poll($_poll_id, $_user_id)
	{
		$query =
		"
			SELECT
				user_id AS 'id'
			FROM
				posts
			WHERE
				posts.id = $_poll_id
			LIMIT 1
			-- polls::is_my_poll()
		";
		$result = \lib\db::get($query, 'id', true);
		if($result && $result == $_user_id)
		{
			return true;
		}
		return false;
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
		$post_id = " WHERE posts.id ='". $_poll_id. "' ";
		if($_poll_id === null)
		{
			$post_id = null;
		}
		$query = "
			SELECT
					posts.*,
					options.*,
					pollstats.*
			FROM
				posts
			LEFT JOIN pollstats
				ON pollstats.post_id = posts.id
			INNER JOIN options
				ON 	options.post_id = posts.id AND
					options.user_id IS NULL AND
					options.option_key LIKE 'answers%'
			$post_id";
		list($limit_start, $limit_end) = \lib\db::pagnation($query, 10);
		$query .= " LIMIT $limit_start, $limit_end ";

		$result = \lib\db::get($query);
		$result = \lib\utility\filter::meta_decode($result);
		return $result;
	}
}
?>