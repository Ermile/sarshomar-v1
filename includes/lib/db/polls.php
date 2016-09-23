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

		// check post_type . if post_type is null return all type of posts
		if(isset($_args['post_type']))
		{
			$post_type = " posts.post_type = 'poll_". $_args['post_type'] . "'";
		}
		else
		{
			$post_type = " posts.post_type LIKE 'poll\_%'";
		}

		// check post_status
		if(isset($_args['post_status']))
		{
			$post_status = " AND posts.post_status = '" .$_args['post_status'] . "'";
		}
		else
		{
			$post_status = null;
		}

		// check users id , retrun post of one person or all person
		if(isset($_args['user_id']))
		{
			$user_id = "AND posts.user_id = " . $_args['user_id'];
		}
		else
		{
			$user_id = null;
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

		// pagnation
		$count_record =
		"
			SELECT
				posts.id
			FROM
				posts
				$join
			WHERE
				$post_type
				$post_status
				$user_id
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
					$post_type
					$post_status
					$user_id
				ORDER BY posts.id DESC
				$limit
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
			\lib\debug::error(T_("user id not found"));
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
			$language = $_args['language'];
		}
		// check title
		if(!isset($_args['title']))
		{
			\lib\debug::fatal(T_("poll title can not be null"));
		}
		else
		{
			$title = $_args['title'];
		}
		// check content
		if(!isset($_args['content']))
		{
			$content = '';
		}
		else
		{
			$content = $_args['content'];
		}
		// check type
		if(!isset($_args['type']))
		{
			$type = "private_select";
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
			if($language == 'fa')
			{
				$publish_date = \lib\utility\jdate::date("Y-m-d");
			}
			else
			{
				$publish_date = date("Y-m-d");
			}
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
			'post_url'         => $slug . $user_id, // insert post id ofter insert record
			'post_content'     => $content,
			'post_type'        => 'poll_' . $type,
			'post_status'      => $status,
			'post_meta'        => null, // update meta after insert answers
			'post_publishdate' => $publish_date
		];

		$result = \lib\db\posts::insert($post_value);

		// new id of poll, posts.id
		$insert_id 	= \lib\db::insert_id();

		if($insert_id)
		{
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
			\lib\debug::error(T_("user id can not be null"));
		}
		else
		{
			$user_id = $_args['user_id'];
		}

		if(!isset($_args['title']))
		{
			\lib\debug::fatal(T_("poll title can not be null"));
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
	public static function get_one($_post_id, $_user_id = null)
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
				LIMIT 1 ";
		$poll =  \lib\db\posts::select($query, "get");

		$answers = \lib\db\answers::get($_post_id);

		return ['poll' => $poll[0] , 'answers' => $answers];
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
				";
		$title = \lib\db\posts::select($query, "get");
		if(isset($title[0]))
		{
			return $title[0];
		}
		else
		{
			return $title;
		}
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
		return $result['title'];
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
		return $result['meta'];
	}


	/**
	 * set answered count to post meta
	 *
	 * @param      <type>  $_poll_id
	 */
	public static function set_result($_args)
	{

		if(isset($_args['poll_id']))
		{
			$poll_id = $_args['poll_id'];
		}
		else
		{
			\lib\debug::error(T_("poll id not found"));
		}


		if(isset($_args['user_id']))
		{
			$user_id = $_args['user_id'];
		}
		else
		{
			\lib\debug::error(T_("user id not found"));
		}

		// get count of answered users for this poll
		$query = "
				SELECT
					COUNT(options.id) as 'count',
                    option_value
                FROM
                    options
                WHERE
                    user_id IS NOT NULL AND
                    post_id = $poll_id AND
                    option_key LIKE 'answer%'
               	GROUP BY
               		option_value
				";
		$count = \lib\db\options::select($query, 'get');

		// update post meta and save cont of answered
		if($count)
		{

			$count_answered = array_sum(array_column($count, 'count'));
			$count = json_encode($count, JSON_UNESCAPED_UNICODE);
				$update =
				"
					UPDATE
						posts
					SET
						posts.post_meta = JSON_REPLACE(posts.post_meta, '$.answers' , '$count')
					WHERE
						posts.id = $poll_id
				";

			\lib\db::query($update);

			//save opt count in op tions table
			$opt_count =
			"
				INSERT INTO
					options
				(post_id, 	user_id,	 option_cat,		option_key, 	 option_value,  option_meta	)
				VALUES
				($poll_id, 	NULL,		'poll_$poll_id',	'stat',			 'opt_count',	'$count'	)
				ON DUPLICATE KEY UPDATE
					option_meta = '$count'
			";

			\lib\db::query($opt_count);
		}

		// set count of answered poll
		$stat_query =
		"
			UPDATE
				options
			SET
				options.option_meta  = option_meta + 1
			WHERE
				options.user_id IS NULL AND
				options.post_id      = $poll_id AND
				options.option_cat   = 'poll_{$poll_id}' AND
				options.option_key   = 'stat' AND
				options.option_value = 'total'
		";

		$update = \lib\db::query($stat_query);
		// if can not update record insert new record
		if(!$update)
		{
			$insert_query =
			"
				INSERT INTO
					options
				SET
					options.post_id      = $poll_id,
					options.option_cat   = 'poll_{$poll_id}',
					options.option_key   = 'stat',
					options.option_value = 'total',
					options.option_meta  = 1
			";
			$insert = \lib\db::query($insert_query);
		}

		$query =
			"
			SELECT
                  options.option_key AS 'key',
                  options.option_value AS 'value'
            FROM
                  options
            WHERE
            	  options.post_id IS NULL AND
                  options.user_id = $user_id AND
                  options.option_cat = 'user_{$user_id}'
			";

		$users_detail_list = \lib\db::get($query,['key', 'value']);
		// list of user details
		foreach ($users_detail_list as $key => $value)
		{
			$v = '$.' . $value;
			$query =
			"
				UPDATE
                	options
                SET
                	options.option_meta =
         						       	IF(options.option_meta IS NULL OR options.option_meta = '',
         						       		'{\"$value\": 1}',
                							IF(
                							   JSON_EXTRACT(options.option_meta, '$v'),
											   JSON_REPLACE(options.option_meta, '$v', JSON_EXTRACT(options.option_meta, '$v') + 1 ),
											   JSON_SET(options.option_meta, '$v', 1)
                							)
                						)
                WHERE
					options.option_cat   = 'poll_{$poll_id}' AND
					options.option_key   = 'stat' AND
					options.option_value = '$key' AND
                	options.post_id 	 = $poll_id
			";
			$update = \lib\db::query($query);
			$update_rows = mysqli_affected_rows(\lib\db::$link);
			if(!$update_rows)
			{
				$query =
				"
					INSERT INTO
	                	options
	                SET
						options.option_cat   = 'poll_{$poll_id}',
						options.option_key   = 'stat',
						options.option_value = '$key',
	                	options.option_meta  = '{\"$value\": 1}',
	                	options.post_id 	 = $poll_id
				";
			$update = \lib\db::query($query);
			}
		}
	}


	/**
	 * return last question for this user
	 * @param  [type] $_user_id [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function get_last($_user_id, $_type = null)
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
				posts.id as id,
				posts.post_title as question,
				posts.post_meta as opt
			FROM posts
			LEFT JOIN `options` ON `options`.post_id = posts.id
			WHERE
				$_type AND
				post_status = 'publish' AND
				posts.id NOT IN
				(
					SELECT post_id FROM options
						WHERE
						`options`.option_cat LIKE 'poll\_%' AND
						`options`.option_key LIKE 'answer\_%'AND
						`options`.user_id = $_user_id
				)
			ORDER BY posts.id ASC
			LIMIT 1
		";

		$result      = \lib\db::get($qry, null, true);

		$returnValue =
		[
			'id'          => null,
			'questionRaw' => null,
			'question'    => null,
			'opt'	      => null,
			'tags'        => null,
		];
		if(isset($result['question']))
		{
			$result['question']         = html_entity_decode($result['question']);
			$returnValue['id']          = $result['id'];
			$returnValue['question']    = $result['question'];
			$returnValue['questionRaw'] = $result['question'];
			$tagList                    = \lib\db\tags::usage($returnValue['id']);
			foreach ($tagList as $key => $value)
			{
				$newValue                = "#". str_replace(' ', '\_', $value);
				$returnValue['tags']     .= $newValue.' ';
				$returnValue['question'] = str_replace($value.' ', $newValue.' ', $returnValue['question']);
			}
		}
		if(isset($result['opt']))
		{
			$returnValue['opt'] = json_decode($result['opt'], true);
			$returnValue['opt'] = array_column($returnValue['opt']['opt'], 'txt', 'key');
		}
		return $returnValue;
	}


	/**
	 * get result of specefic item
	 * @param  [type] $_poll_id [description]
	 * @param  [type] $_value   [description]
	 * @param  [type] $_key     [description]
	 * @return [type]           [description]
	 */
	public static function get_result($_poll_id, $_value = null, $_key = null)
	{
		// get answers form post meta
		$poll = self::get_poll($_poll_id);
		$meta = json_decode($poll['meta'], true);

		$opt = $meta['opt'];
		$answers = $meta['answers'];
		$answers = json_decode($answers, true);

		if(!is_array($opt))
		{
			return ;
		}

		$final_result = [];
		foreach ($opt as $key => $value) {
			$count = 0;
			foreach ($answers as $k => $result) {
				if($result['option_value'] == $value['key'])
				{
					$count = $result['count'];
				}
			}
			$final_result[$value['txt']] =  $count;
		}

		$result           = [];
		$result['title']  = $poll['title'];
		$result['url']    = 'sp_' .  $poll['url'];
		$result['result'] = $final_result;
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
}
?>