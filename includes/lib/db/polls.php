<?php
namespace lib\db;

/** work with polls **/
class polls
{
	/**
	 * this library work with acoount
	 * v3.1
	 */

	/**
	 * get list of posts wthi post_type = polls_(|.*)
	 *
	 * @param      array  $_args  user_id, post_type, start(limit), end(limit) ,
	 *
	 * @return     array  mysql result
	 */
	public static function xget($_args = [])
	{
		$where = [];

		// check post_type . if post_type is null return all type of posts
		if(isset($_args['post_type']))
		{
			$where[] = "posts.post_type = '". $_args['post_type'] . "'";
		}
		else
		{
			$where[] = "posts.post_type LIKE 'poll\_%'";
		}

		// check post_id
		if(isset($_args['id']))
		{
			$where[] = "posts.id = ". $_args['id'];
		}

		// check post_status
		if(isset($_args['post_status']))
		{
			$where[] = "posts.post_status = '" .$_args['post_status'] . "'";
		}

		// check users id , retrun post of one person or all person
		if(isset($_args['user_id']))
		{
			$where[] = "posts.user_id = " . $_args['user_id'];
		}

		if(isset($_args['filter']) && isset($_args['value']))
		{
			$filter = $_args['filter'];
			$value  = $_args['value'];
			$join =
			"
				INNER JOIN
					options
				ON  options.post_id = posts.id AND
					options.user_id IS NULL AND
					options.option_cat = 'poll_' & posts.id AND
					options.option_key = '$filter' AND
					options.option_value = '$value'
			";
		}
		else
		{
			$join = "";
		}

		$where = join($where, " AND ");
		// pagnation
		$count_record =
		"
			SELECT
				posts.id
			FROM
				posts
				$join
			WHERE
				$where
		";

		list($limit_start, $length) = \lib\db::pagnation($count_record, 10);
		$limit = " LIMIT $limit_start, $length ";

		// creat query string
		// fields we not show: date_modified , post_meta, user_id
		$query = "
				SELECT
					posts.id,
					posts.post_language 		as 'language',
					posts.post_title 			as 'title',
					posts.post_slug 			as 'slug',
					posts.post_url 				as 'url',
					posts.post_content 			as 'content',
					posts.post_type 			as 'type',
					posts.post_comment 			as 'comment',
					posts.post_count 			as 'count',
					posts.post_order 			as 'order',
					posts.post_status 			as 'status',
					posts.post_parent 			as 'parent',
					posts.post_publishdate 		as 'publishdate'
				FROM
					posts
					$join
				WHERE
					$where
				ORDER BY posts.id DESC
				$limit
				-- Get post polls::xget()
					";
		return \lib\db\posts::select($query, "get");
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

		// get slug string
		$slug =  \lib\utility\filter::slug($_args['title']);
		//  check 		// check user_id
		if(!isset($_args['user_id']))
		{
			return false;
		}
		else
		{
			$user_id = $_args['user_id'];
		}
		// check language
		if(!isset($_args['language']))
		{
			$language = substr(\lib\router::get_storage('language'), 0, 2);
		}
		else
		{
			if(strlen($_args['language']) > 2)
			{
				$language = substr(\lib\router::get_storage('language'), 0, 2);
			}
			else
			{
				$language = $_args['language'];
			}
		}
		// check title
		if(!isset($_args['title']))
		{
			return false;
		}
		else
		{
			$title = $_args['title'];
		}
		// check parent
		if(!isset($_args['parent']))
		{
			$parent = null;
		}
		else
		{
			$parent = $_args['parent'];
		}
		// check meta
		if(!isset($_args['meta']))
		{
			$meta = null;
		}
		else
		{
			$meta = $_args['meta'];
		}
		// check content
		if(!isset($_args['content']))
		{
			$content = null;
		}
		else
		{
			$content = $_args['content'];
		}
		// check type
		if(!isset($_args['type']))
		{
			return false;
		}
		else
		{
			$type = $_args['type'];
		}
		// check status
		if(!isset($_args['status']))
		{
			$status = "draft";
		}
		else
		{
			$status = $_args['status'];
		}
		// check publish_date
		if(!isset($_args['publish_date']))
		{
			$publish_date = null;
		}
		else
		{
			$publish_date = $_args['publish_date'];
		}

		$post_value =
		[
			'user_id'          => $user_id,
			'post_language'    => $language,
			'post_title'       => $title,
			'post_slug'        => $slug,
			'post_url'         => time(). '_'. $user_id, // insert post id ofter insert record
			'post_content'     => $content,
			'post_type'        => $type,
			'post_status'      => $status,
			'post_parent'      => $parent,
			'post_meta'        => $meta,
			'post_publishdate' => $publish_date
		];

		$result = \lib\db\posts::insert($post_value);

		// new id of poll, posts.id
		$insert_id 	= \lib\db::insert_id();

		if($insert_id)
		{

			// UPDATE posts SET post_meta = [answers,...] WHERE posts.id = [id]
			$short_url = \lib\utility\shortURL::encode($insert_id);

			// $title = preg_replace("/[\n\t\s\,\-\(\)\!\@\#\$\%\^\&\/\.\?\<\>\|\{\}\[\]\"\'\:\;\*]/", "_", $title);
			$title = str_replace(" ", "_", $title);
			$url = '$/' . $short_url . '/'. $title;

			$set_url = \lib\db\polls::update(['post_url' => $url], $insert_id );

			return $insert_id;
		}
		else
		{
			return false;
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
			'user_id'     => $_args['user_id'],
			'title'       => $_args['title']
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
			return \lib\db\answers::insert(['poll_id' => $insert_id , 'answers' => $answers_value]);
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
			$_type = "post_type LIKE 'poll\_%'";
		}
		else
		{
			$_type = "post_type = 'poll_". $_type. "'";
		}
		// calc user id if exist
		if($_user_id)
		{
			$_user_id = "AND user_id = $_user_id";
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
	 * return one querstion whit answers
	 * to fill edit form
	 * @param  [type] $_post_id 	[description]
	 * @param  string $_users_id    [description]
	 * @return [type]           	[description]
	 */
	public static function get_for_edit($_post_id, $_user_id = null)
	{
		//check users id
		if($_user_id != null) {
			$_user_id = " AND user_id = $_user_id ";
		}
		$query = "
				SELECT
					id,
					post_language 		as 'language',
					post_title 			as 'title',
					post_slug 			as 'slug',
					post_url 			as 'url',
					post_content 		as 'content',
					post_type 			as 'type',
					post_comment 		as 'comment',
					post_count 			as 'count',
					post_order 			as 'order',
					post_status 		as 'status',
					post_parent 		as 'parent',
					post_meta	     	as 'meta',
					post_publishdate 	as 'publishdate'
				FROM posts
				WHERE
					posts.id = $_post_id
					$_user_id
				LIMIT 1
				--  polls::get_for_edit()
				";
		$poll =  \lib\db::get($query, null);
		$poll = \lib\utility\filter::meta_decode($poll);
		if($poll &&  is_array($poll) && !empty($poll))
		{
			$answers = \lib\db\answers::get($_post_id);
			return ['poll' => $poll , 'answers' => $answers];
		}
		else
		{
			return false;
		}
	}


	/**
	 * get title and meta of poll
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  The poll.
	 */
	public static function get_poll($_poll_id) {
		$query = "
				SELECT
					id,
					post_language 		as 'language',
					post_title 			as 'title',
					post_slug 			as 'slug',
					post_url 			as 'url',
					post_content 		as 'content',
					post_type 			as 'type',
					post_comment 		as 'comment',
					post_count 			as 'count',
					post_order 			as 'order',
					post_status 		as 'status',
					post_parent 		as 'parent',
					post_meta	     	as 'meta',
					post_publishdate 	as 'publishdate'
				FROM
					posts
				WHERE
					id = $_poll_id
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
	public static function get_previous_url($_user_id)
	{
		$query =
		"
			SELECT
				posts.post_url AS 'url'
			FROM
				polldetails
			INNER JOIN posts ON posts.id = polldetails.post_id
			WHERE
				polldetails.user_id = $_user_id
			ORDER BY
				polldetails.id DESC
			LIMIT 1
			-- polls::get_previous_url()
			-- to get previous of answered this user
		";
		$result= \lib\db::get($query, 'url', true);
		return $result;
	}

	/**
	 * return last question for this user
	 * @param  [type] $_user_id [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function get_last($_user_id, $_type = null, $_poll_id = null)
	{
		// calc type if needed
		if($_type === null)
		{
			$_type = "post_type LIKE 'poll\_%'";
		}
		else
		{
			$_type = "post_type = 'poll_". $_type. "'";
		}

		$qry ="
			SELECT
			-- Fields
				posts.id,
				posts.post_language 		as 'language',
				posts.post_title 			as 'title',
				posts.post_slug 			as 'slug',
				posts.post_url 			as 'url',
				posts.post_content 		as 'content',
				posts.post_type 			as 'type',
				posts.post_comment 		as 'comment',
				posts.post_count 			as 'count',
				posts.post_order 			as 'order',
				posts.post_status 		as 'status',
				posts.post_parent 		as 'parent',
				posts.post_meta	     	as 'meta',
				posts.post_publishdate 	as 'publishdate'

			FROM
				posts
			-- To get options of this poll
			LEFT JOIN options ON options.post_id = posts.id

			WHERE
				-- Get post_type and publish status
				$_type AND
				post_status = 'publish' AND ";
				if(!is_null($_poll_id))
				{
					$qry .=" posts.id = $_poll_id AND ";
				}
				$qry .="
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
							options.post_id
						FROM
							options
						WHERE
							options.option_cat = 'poll_' & posts.post_parent AND
							options.option_key = 'answer_$_user_id' AND
							options.user_id = $_user_id	 AND
							options.post_id = posts.post_parent AND
							-- Get opt has lock on tree
							options.option_value IN
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

		$result  = \lib\db::get($qry, null);

		$result = \lib\utility\filter::meta_decode($result);

		if(isset($result[0]))
		{
			return $result[0];
		}
		return false;
	}


	/**
	 * change return format
	 *
	 * @param      <type>  $_result  The result
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public static function poll_format($_result)
	{
		$returnValue =
		[
			'id'          => null,
			'questionRaw' => null,
			'question'    => null,
			'opt'	      => null,
			'url'	      => null,
			'tags'        => null,
		];
		if(isset($_result['title']))
		{
			$_result['title']         = html_entity_decode($_result['title']);
			$returnValue['id']          = $_result['id'];
			$returnValue['url']         = $_result['url'];
			$returnValue['question']    = $_result['title'];
			$returnValue['questionRaw'] = $_result['title'];
			$tagList                    = \lib\db\tags::usage($returnValue['id']);
			foreach ($tagList as $key => $value)
			{
				$newValue                = "#". str_replace(' ', '\_', $value);
				$returnValue['tags']     .= $newValue.' ';
				$returnValue['question'] = str_replace($value.' ', $newValue.' ', $returnValue['question']);
			}
		}

		if(isset($_result['meta']))
		{
			$returnValue['opt'] = $_result['meta'];
			if(is_array($returnValue['opt']['opt']))
			{
				$returnValue['opt'] = array_column($returnValue['opt']['opt'], 'txt', 'key');
			}
			else
			{
				return null;
			}
		}
		return $returnValue;
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
		$field = array_keys($_field_meta);
		$meta  = array_values($_field_meta);
		$query = [];
		foreach ($field as $key => $value) {
			$query[] = " $value = JSON_MERGE(JSON_EXTRACT($value, '$'), '{$meta[$key]}')";
		}
		$query = join($query, ',');
		$query =
		"
			UPDATE
				posts
			SET
				$query
			WHERE
				posts.id = $_poll_id
			-- polls::merge_meta()
			-- add new json to existing meta of post_meta
		";
		$result = \lib\db::query($query);
		return $result;
	}

	/**
	 * get list of questions that this user answered
	 * @param  [type] $_user_id [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function getAnweredList($_user_id, $_type = 'sarshomar', $_return = null)
	{
		// calc type if needed
		if($_type === null)
		{
			$_type = "post_type LIKE 'poll\_%'";
		}
		else
		{
			$_type = "post_type = 'poll_". $_type. "'";
		}

		$qry ="SELECT * FROM posts
			LEFT JOIN `options` ON `options`.post_id = posts.id

			WHERE
				$_type AND
				user_id = $_user_id AND
				option_cat = 'poll_$_user_id' AND
				option_key LIKE 'answer\_%' AND
				option_status = 'enable'
		";

		$result = \lib\db::get($qry, $_return);
		return $result;
	}


	/**
	 * save poll into database
	 * @return [type] [description]
	 */
	public static function save($_input, $_user_id)
	{
		// return false if count of input value less than 3
		// 1 question
		// 2 answer or more
		if(count($_input) < 3 || count($_input) > 10 || !isset($_input['question']))
		{
			return false;
		}
		// extract question
		$question = $_input['question'];
		unset($_input['question']);
		// save question into post table
		$saveResult = self::saveQuestion($question, $_input, $_user_id);
		// return final result
		return $saveResult;
	}


	/**
	 * save question into post table
	 * @param  [type] $_question    [description]
	 * @param  [type] $_answersList [description]
	 * @return [type]               [description]
	 */
	public static function saveQuestion($_question, $_answersList, $_user_id)
	{
		$slug         = \lib\utility\filter::slug($_question);
		$url          = 'civility/'.$_user_id.'/'.$slug;
		$myAnswersList = json_encode($_answersList, JSON_UNESCAPED_UNICODE);
		$pubDate      = date('Y-m-d H:i:s');
		// create query string
		$qry = "INSERT INTO posts
		(
			`post_language`,
			`post_title`,
			`post_slug`,
			`post_url`,
			`post_meta`,
			`post_type`,
			`post_status`,
			`post_publishdate`,
			`user_id`
		)
		VALUES
		(
			'fa',
			'$_question',
			'$slug',
			'$url',
			'$myAnswersList',
			'poll',
			'draft',
			'$pubDate',
			$_user_id
		)";
		// run query
		$result        = \lib\db::query($qry);
		// return last insert id
		$questionId    = \lib\db::insert_id();
		// save answers into options table
		$saveAnsStatus = self::saveAnswersList($_answersList, $questionId);
		return $saveAnsStatus;
	}


	/**
	 * save answers into options table
	 * @param  [type] $_answersList raw answer list
	 * @return [type]               [description]
	 */
	public static function saveAnswersList($_answersList, $_post_id)
	{
		$answers = [];
		$max_ans = 10;
		// foreach answers exist fill the array
		foreach ($_answersList as $key => $value)
		{
			$answers[$key]['txt'] = $value;
		}
		// decode for saving into db
		$answers     = json_encode($answers, JSON_UNESCAPED_UNICODE);
		$option_data =
		[
			'post'   => $_post_id,
			'cat'    => 'meta_polls',
			'key'    => 'answers_'.$_post_id,
			'value'  => "",
			'meta'   => $answers,
			'status' => 'enable',
		];
		// save in options table and if successful return session_id
		return \lib\utility\option::set($option_data, true);
	}


	/**
	 * delete answers of specefic user
	 * @param  [type] $_user_id [description]
	 * @return [type]           [description]
	 */
	public static function removeUserAnswers($_user_id)
	{
		$qry = "DELETE FROM options
			WHERE
				user_id = $_user_id AND
				option_cat = 'poll_$_user_id' AND
				option_key LIKE 'answer\_%'
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
		if(\lib\db\answers::delete($_post_id))
		{
			return \lib\db\posts::delete($_poll_id);
		}
		else
		{
			return false;
		}
	}
}
?>