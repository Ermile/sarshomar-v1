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

		// insert data to polldetails table
		$insert_polldetails =
		"
			INSERT INTO
				polldetails
			SET
				user_id = $user_id,
				post_id = $poll_id,
				opt     = '$opt_key',
				type    = NULL,
				txt     = '$opt_txt',
				user_profile =
				(
					SELECT
					CONCAT('[', GROUP_CONCAT(JSON_OBJECT(option_key, option_value)), ']') AS JSON
					FROM
						options
					WHERE
						user_id    = $user_id AND
						option_cat = 'user_detail_$user_id'
				),
				visitor_id = 1
		";
		$insert_polldetails = \lib\db::query($insert_polldetails);


		$user_profile_data = \lib\db\profiles::get_profile_data($user_id);


		$set = [];
		$set_for_insert = [];
		foreach ($user_profile_data as $key => $value) {
			if(\lib\db\filters::support_filter($key))
			{
				$v = '$.' . $opt_key. '."'. $value. '"';
				$set[] =
				"
					pollstats.$key =
				       	IF(pollstats.$key IS NULL OR pollstats.$key = '',
					       		'{\"$opt_key\":{\"$value\":1}}',
							IF(
							   JSON_EXTRACT(pollstats.$key, '$v'),
							   JSON_REPLACE(pollstats.$key, '$v', JSON_EXTRACT(pollstats.$key, '$v') + 1 ),
							   JSON_INSERT(pollstats.$key, '$.$opt_key',JSON_OBJECT(\"$value\",1))
							)
						)
	        	";
	        	$set_for_insert[] = " pollstats.$key = '{\"$opt_key\":{\"$value\":1}}' ";
			}
		}

		$set = join($set, " , ");
		$pollstats_update_query =
		"
			UPDATE
				pollstats
			SET
				pollstats.total = pollstats.total + 1,
				$set
			WHERE
				pollstats.post_id = $poll_id
		";

		$pollstats_update = \lib\db::query($pollstats_update_query);

		if(!$pollstats_update)
		{
			$set_for_insert = join($set_for_insert, " , ");
			$pollstats_insert_query =
			"
				INSERT INTO
					pollstats
				SET
					pollstats.post_id = $poll_id,
					pollstats.total = 1,
					$set_for_insert
			";
			$pollstats_insert = \lib\db::query($pollstats_insert_query);
		}


		// update post meta and save count answered in to meta
		$update_posts_meta =
		"
			UPDATE
            	posts
            SET
            	posts.post_meta =
			       	IF(posts.post_meta IS NULL OR posts.post_meta = '',
			       		'{\"answers\":{\"$opt_key\":1}}',
						IF(
						   JSON_EXTRACT(posts.post_meta, '$.answers.$opt_key'),
						   JSON_REPLACE(posts.post_meta, '$.answers.$opt_key',
						   JSON_EXTRACT(posts.post_meta, '$.answers.$opt_key') + 1 ),
						   JSON_SET(posts.post_meta, '$.answers', JSON_OBJECT(\"$opt_key\",1))
					      )
					)
            WHERE
            	posts.id 	 = $poll_id
		";
		$update_posts_meta = \lib\db::query($update_posts_meta);
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
		if(!isset($poll['meta']) || empty($poll))
		{
			return false;
		}

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
				options.user_id IS NULL AND
				options.post_id      = $_poll_id AND
				options.option_cat   = 'poll_$_poll_id' AND
				options.option_key   = 'stat' AND
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