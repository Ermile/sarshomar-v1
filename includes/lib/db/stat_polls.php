<?php
namespace lib\db;

/** work with polls **/
class stat_polls
{
	/**
	 * this library work with acoount
	 * v1.0
	 */


	/**
	 * get list of questions that this user answered
	 * @param  [type] $_user_id [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function answeredInPeriod($_user_id, $_period = 6)
	{
		$qry ="SELECT count(id) as count FROM options
			WHERE
				user_id = $_user_id AND
				option_cat = 'polls_$_user_id' AND
				option_key LIKE 'answer\_%' AND
				option_status = 'enable' AND
				date_modified >= DATE_SUB(NOW(),INTERVAL $_period HOUR)
		";

		$result = (int) \lib\db::get($qry, 'count', true);
		return $result;
	}


	/**
	 * set answered count to post meta
	 *
	 * @param      <type>  $_poll_id
	 */
	public static function set_poll_result($_args)
	{

		if(isset($_args['poll_id']))
		{
			$poll_id = $_args['poll_id'];
		}
		else
		{
			return false;
		}

		if(isset($_args['user_id']))
		{
			$user_id = $_args['user_id'];
		}
		else
		{
			return false;
		}

		if(isset($_args['opt_key']))
		{
			$opt_key = $_args['opt_key'];
		}
		else
		{
			return false;
		}

		if(isset($_args['opt_txt']))
		{
			$opt_txt = $_args['opt_txt'];
		}
		else
		{
			$opt_txt = null;
		}

		/**
		 * set count total answere + 1
		 * to get sarshomar total answered
		 */
		self::set_sarshomar_total_answered();


		// update post meta and save count answered in to meta
		$update_posts_meta =
		"
			UPDATE
            	posts
            SET
            	posts.post_meta =
			       	IF(posts.post_meta IS NULL OR posts.post_meta = '',
			       		'{\"answeres\":{\"$opt_key\":1}}',
						IF(
						   JSON_EXTRACT(posts.post_meta, '$.answeres.$opt_key'),
						   JSON_REPLACE(posts.post_meta, '$.answeres.$opt_key',
						   JSON_EXTRACT(posts.post_meta, '$.answeres.$opt_key') + 1 ),
						   JSON_SET(posts.post_meta, '$.answeres', JSON_OBJECT(\"$opt_key\",1))
					      )
					)
            WHERE
            	posts.id 	 = $poll_id
		";
		$update_posts_meta = \lib\db::query($update_posts_meta);

		//count answered to this poll in option table

		// example $.opt_1
		$json_opt = '$.'. $opt_key;

		$opt_count =
		"
			UPDATE
				options
			SET
				options.option_meta =
					IF(options.option_meta IS NULL OR options.option_meta = '',
				       		'{\"$opt_key\":1}',
						IF(
						   JSON_EXTRACT(options.option_meta, '$json_opt'),
						   JSON_REPLACE(options.option_meta, '$json_opt',
						   JSON_EXTRACT(options.option_meta, '$json_opt') + 1 ),
						   JSON_INSERT(options.option_meta, '$json_opt', 1)
						)
					)
			WHERE
				options.user_id IS NULL AND
				options.post_id      = $poll_id AND
				options.option_cat   = 'poll_$poll_id' AND
				options.option_key   = 'stat' AND
				options.option_value = 'opt_count'
		";

		$update = \lib\db::query($opt_count);
		$update_rows = mysqli_affected_rows(\lib\db::$link);

		if(!$update_rows)
		{
			$opt_count =
			"
				INSERT INTO
					options
				SET
					options.user_id  	 = NULL,
					options.option_meta  = '{\"$opt_key\":1}',
					options.post_id      = $poll_id,
					options.option_cat   = 'poll_$poll_id',
					options.option_key   = 'stat',
					options.option_value = 'opt_count'
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
				options.option_cat   = 'poll_$poll_id' AND
				options.option_key   = 'stat' AND
				options.option_value = 'total'
		";

		$update = \lib\db::query($stat_query);
		// if can not update record insert new record
		$update_rows = mysqli_affected_rows(\lib\db::$link);
		if(!$update_rows)
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

		// get users detail and save to option meta
		$query =
			"
			SELECT
                  options.option_key 	AS 'key',
                  options.option_value 	AS 'value'
            FROM
                  options
            WHERE
            	  options.post_id IS NULL AND
                  options.user_id = $user_id AND
                  options.option_cat = 'user_detail_$user_id'
			";

		$users_detail_list = \lib\db::get($query,['key', 'value']);

		// list of user details
		foreach ($users_detail_list as $key => $value)
		{
			$v = '$.' . $opt_key. '."'. $value. '"';

			$query =
			"
				UPDATE
                	options
                SET
                	options.option_meta =
         						       	IF(options.option_meta IS NULL OR options.option_meta = '',
         						       		'{\"$opt_key\":{\"$value\":1}}',
                							IF(
                							   JSON_EXTRACT(options.option_meta, '$v'),
											   JSON_REPLACE(options.option_meta, '$v', JSON_EXTRACT(options.option_meta, '$v') + 1 ),
											   JSON_INSERT(options.option_meta, '$.$opt_key',JSON_OBJECT(\"$value\",1))
                							)
                						)
                WHERE
					options.option_cat   = 'poll_$poll_id' AND
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
	                	options.option_meta  = '{\"$opt_key\":{\"$value\":1}}',
	                	options.post_id 	 = $poll_id
				";
				$insert = \lib\db::query($query);
			}
		}
	}


	/**
	 * get result of specefic item
	 * @param  [type] $_poll_id [description]
	 * @param  [type] $_value   [description]
	 * @param  [type] $_key     [description]
	 * @return [type]           [description]
	 */
	public static function get_telegram_result($_poll_id, $_value = null, $_key = null)
	{
		// get answers form post meta
		$poll = \lib\db\polls::get_poll($_poll_id);
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
		$result['url']    = $poll['url'];
		$result['result'] = $final_result;
		return $result;
	}



	public static function get_result($_poll_id)
	{

		// get poll meta to get all opt of this poll
		$poll = \lib\db\polls::get_poll($_poll_id);

		$poll_meta = json_decode($poll['meta'], JSON_UNESCAPED_UNICODE);

		if(isset($poll_meta['opt']))
		{
			$poll_opt = $poll_meta['opt'];
		}
		else
		{
			return false;
		}

		$query =
		"
			SELECT
				options.option_value AS 'value',
				options.option_meta AS 'meta'
			FROM
				options
			WHERE
				options.post_id = $_poll_id AND
				options.user_id IS NULL AND
				options.option_cat = 'poll_$_poll_id' AND
				options.option_key = 'stat' AND
				options.option_value = 'opt_count'
		";
		$result = \lib\db::get($query, ['value', 'meta']);
		if($result)
		{
			$opt_count = json_decode($result['opt_count'], JSON_UNESCAPED_UNICODE);
			$result = [];
			// $result['id'] = $poll['id'];
			$result['title'] = $poll['title'];
			// $result['url'] = $poll['url'];
			foreach ($poll_opt as $key => $value) {
				if(isset($opt_count[$value['key']]))
				{
					$name = $value['txt'];
					$data = [$opt_count[$value['key']]];
				}
				else
				{
					$name = $value['txt'];
					$data = [0];
				}
				$result['data'][] = ['name' => $name,'data' => $data];
			}
			return $result;
		}
		else
		{
			return null;
		}
		return false;
	}


	/**
	 * Sets the sarshomar total answered.
	 */
	public static function set_sarshomar_total_answered()
	{
		// set count of answered poll
		$stat_query =
		"
			UPDATE
				options
			SET
				options.option_value  = option_value + 1
			WHERE
				options.user_id IS NULL AND
				options.post_id IS NULL AND
				options.option_cat   = 'sarshomar_total_answered' AND
				options.option_key   = 'total_answered'
		";

		$update = \lib\db::query($stat_query);
		// if can not update record insert new record
		$update_rows = mysqli_affected_rows(\lib\db::$link);
		if(!$update_rows)
		{
			$insert_query =
			"
				INSERT INTO
					options
				SET
					options.post_id      = NULL,
					options.user_id      = NULL,
					options.option_cat   = 'sarshomar_total_answered',
					options.option_key   = 'total_answered',
					options.option_value = 1
			";
			$insert = \lib\db::query($insert_query);
		}
	}


	public static function get_sarshomar_total_answered()
	{
		$stat_query =
		"
			SELECT
				options.option_value AS 'count'
			FROM
				options
			WHERE
				options.user_id IS NULL AND
				options.post_id IS NULL AND
				options.option_cat   = 'sarshomar_total_answered' AND
				options.option_key   = 'total_answered'
			LIMIT 1
		";
		$total = \lib\db::get($stat_query, 'count', true);
		return intval($total);
	}
}
?>