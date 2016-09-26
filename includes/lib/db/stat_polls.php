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
                  options.option_cat = 'user_detail_{$user_id}'
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
}
?>