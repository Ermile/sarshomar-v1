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

		$user_profile_data = \lib\db\profiles::get_profile_data($user_id);

		$set = [];
		$set_for_insert = [];
		// save pollstats.result field
		$set[] =
		"
			pollstats.result =
		       	IF(pollstats.result IS NULL OR pollstats.result = '',
			       		'{\"$opt_key\":1}',
					IF(
					   JSON_EXTRACT(pollstats.result, '$.$opt_key'),
					   JSON_REPLACE(pollstats.result, '$.$opt_key', JSON_EXTRACT(pollstats.result, '$.$opt_key') + 1 ),
					   JSON_SET(pollstats.result, '$.$opt_key', 1)
					)
				)
    	";
    	$set_for_insert[] = " pollstats.result = '{\"$opt_key\":1}' ";
    	// set profile result
    	$support_filter = \lib\db\filters::support_filter();
		foreach ($support_filter as $key => $value) {
			if(isset($user_profile_data[$value]))
			{
				$v = '$.' . $opt_key. '."'. $user_profile_data[$value]. '"';
				$set[] =
				"
					pollstats.$value =
				       	IF(pollstats.$value IS NULL OR pollstats.$value = '',
					       		'{\"$opt_key\":{\"$value\":1}}',
							IF(
							   JSON_EXTRACT(pollstats.$value, '$v'),
							   JSON_REPLACE(pollstats.$value, '$v', JSON_EXTRACT(pollstats.$value, '$v') + 1 ),
							   JSON_INSERT(pollstats.$value, '$.$opt_key',JSON_OBJECT(\"{$user_profile_data[$value]}\",1))
							)
						)
	        	";
	        	$set_for_insert[] = " pollstats.$value = '{\"$opt_key\":{\"{$user_profile_data[$value]}\":1}}' ";
			}
			else
			{
				// undifined
				$v = '$.' . $opt_key. '.undefined';
				$set[] =
				"
					pollstats.$value =
				       	IF(pollstats.$value IS NULL OR pollstats.$value = '',
					       		'{\"$opt_key\":{\"undefined\":1}}',
							IF(
							   JSON_EXTRACT(pollstats.$value, '$v'),
							   JSON_REPLACE(pollstats.$value, '$v', JSON_EXTRACT(pollstats.$value, '$v') + 1 ),
							   JSON_INSERT(pollstats.$value, '$.$opt_key',JSON_OBJECT(\"undefined\",1))
							)
						)
	        	";
	        	$set_for_insert[] = " pollstats.$value = '{\"$opt_key\":{\"undefined\":1}}' ";
			}
		}
		$set[] = " pollstats.total = pollstats.total + 1 ";
		$set = join($set, " , ");
		$pollstats_update_query =
		"
			UPDATE
				pollstats
			SET
				$set
			WHERE
				pollstats.post_id = $poll_id
			-- update poll stat result
			-- stat_polls::set_poll_result()
		";

		$pollstats_update = \lib\db::query($pollstats_update_query);
		$update_rows = mysqli_affected_rows(\lib\db::$link);
		if(!$update_rows)
		{
			$set_for_insert[] = " pollstats.post_id = $poll_id ";
			$set_for_insert[] = " pollstats.total = 1 ";
			$set_for_insert = join($set_for_insert, " , ");
			$pollstats_insert_query =
			"
				INSERT INTO
					pollstats
				SET
					$set_for_insert
				-- stat_polls::set_poll_result()
				-- insert poll stat result
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
   			-- stat_polls::set_poll_result()
            -- update post_meta and save answered count to post_meta

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
		$meta = $poll['meta'];
		// $meta = json_decode($poll['meta'], true);

		$opt = $meta['opt'];
		$answers = $meta['answers'];
		if(!is_array($answers))
		{
			$answers = [$answers];
		}
		if(!is_array($opt))
		{
			return ;
		}

		$final_result = [];
		$count = 0;
		foreach ($opt as $key => $value) {
			$opt_key = $value['key'];
			$final_result[$value['txt']] =  0;
			if(!array_key_exists($opt_key, $answers))
			{
				continue;
			}
			$count += $answers[$opt_key];
			$final_result[$value['txt']] =  $answers[$opt_key];
		}
		$result           = [];
		$result['count']  = $count;
		$result['title']  = $poll['title'];
		$result['url']    = $poll['url'];
		$result['result'] = $final_result;
		return $result;
	}



	public static function get_result($_poll_id, $_type = null, $_mode = "highcharts")
	{
		// get poll meta to get all opt of this poll
		$poll = \lib\db\polls::get_poll($_poll_id);
		if(!isset($poll['meta']) || empty($poll))
		{
			return false;
		}

		$poll_meta = $poll['meta'];
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
				*
			FROM
				pollstats
			WHERE
				post_id = $_poll_id
			LIMIT 1
			-- stat_polls::get_result()
		";

		$result = \lib\db::get($query, null);

		$result = \lib\utility\filter::meta_decode($result, "/.*/");

		if(isset($result[0]))
		{
			$result = $result[0];
		}
		else
		{
			return null;
		}
		if($result)
		{
			if($_type)
			{
				if(is_array($_type))
				{
					$array_result = [];
					foreach ($_type as $key => $value) {
						$array_result[$value] =  self::process_result($poll, $poll_opt, $result, $value);
					}
					if($_mode == "highcharts")
					{
						$array_result = self::high_charts_mod($array_result);
					}
					return $array_result;
				}
				elseif($_type == "*")
				{
					$array_result = [];
					foreach ($result as $key => $value) {
						switch ($key) {
							case 'id':
							case 'post_id':
							case 'total':
							case 'meta':
								continue;
								break;

							case 'result':

								$stat_result = [];
								$stat_result['title'] = $poll['title'];

								foreach ($poll_opt as $k => $v) {
									if(isset($result['result'][$v['key']]))
									{
										$name = $v['txt'];
										$data = [$result['result'][$v['key']]];
									}
									else
									{
										$name = $v['txt'];
										$data = [0];
									}
									$stat_result['data'][] = ['name' => $name,'data' => $data];
								}
								if($_mode == "highcharts")
								{
									if(isset($stat_result['data']))
									{
										$stat_result['data'] = json_encode($stat_result['data'], JSON_UNESCAPED_UNICODE);
									}
								}
								$array_result[$key] = $stat_result;
								break;
							default:
								if($_mode == "highcharts")
								{
									$array_result[$key] =  self::high_charts_mod(self::process_result($poll, $poll_opt, $result, $key));
								}
								else
								{
									$array_result[$key] =  self::process_result($poll, $poll_opt, $result, $key);
								}
								break;
						}
					}
					return $array_result;
				}
				elseif(is_string($_type))
				{
					return self::process_result($poll, $poll_opt, $result, $_type);
				}
			}
			else
			{
				$stat_result = [];
				$stat_result['title'] = $poll['title'];

				foreach ($poll_opt as $key => $value) {
					if(isset($result['result'][$value['key']]))
					{
						$name = $value['txt'];
						$data = [$result['result'][$value['key']]];
					}
					else
					{
						$name = $value['txt'];
						$data = [0];
					}
					$stat_result['data'][] = ['name' => $name,'data' => $data];
				}
				if($_mode == "highcharts")
				{
					if(isset($stat_result['data']))
					{
						$stat_result['data'] = json_encode($stat_result['data'], JSON_UNESCAPED_UNICODE);
					}
					return $stat_result;
				}
				return $stat_result;
			}
		}
		else
		{
			return null;
		}
	}

	/**
	 * prosses the result
	 *
	 * @param      <type>  $_poll      The poll
	 * @param      <type>  $_poll_opt  The poll option
	 * @param      <type>  $_result    The result
	 * @param      <type>  $_type      The type
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public static function process_result($_poll, $_poll_opt, $_result, $_type)
	{
		$poll     = $_poll;
		$poll_opt = $_poll_opt;
		$result   = $_result;

		// other chart
		$stat_result = [];
		$stat_result['title'] = $poll['title'];
		// get max result
		$max =[];
		if(!is_array($result[$_type]))
		{
			$result[$_type] = [];
		}

		foreach ($result[$_type] as $key => $value) {
			if(is_array($value))
			{
				$max = array_merge($max, $value);
			}
		}

		foreach ($poll_opt as $key => $value)
		{
			$stat_result[$value['key']]['name'] = $value['txt'];
			foreach ($max as $city => $count)
			{
				if(isset($result[$_type][$value['key']][$city]))
				{
					if(isset($stat_result[$value['key']]['data'][$city]))
					{
						array_push($stat_result[$value['key']]['data'][$city], $result[$_type][$value['key']][$city]);
					}
					else
					{
						if(isset($stat_result[$value['key']]))
						{
							$stat_result[$value['key']]['data'][$city] = $result[$_type][$value['key']][$city];
						}
						else
						{
							$stat_result[$value['key']]['data'] = [$city => $result[$_type][$value['key']][$city]];
						}
					}
				}
				else
				{
					if(isset($stat_result[$value['key']]))
					{
						$stat_result[$value['key']]['data'][$city] = 0;
					}
					else
					{
						$stat_result[$value['key']]['data'] = [$city => 0];
					}
				}
			}
		}
		return $stat_result;
	}

	/**
	 * change stat poll result to highcharts mode
	 * http://www.highcharts.com/
	 *
	 * @param      <type>         $_result  The result
	 *
	 * @return     array|boolean  ( description_of_the_return_value )
	 */
	public static function high_charts_mod($_result)
	{
		if(!is_array($_result))
		{
			return false;
		}
		$title = null;
		$categories = null;
		$result = [];
		foreach ($_result as $key => $value) {
			if($key == 'title')
			{
				$title = $value;
				continue;
			}
			if(is_array($value))
			{
				if(isset($value['data']))
				{
					$categories = array_keys($value['data']);
					$result[] = ['name' => $value['name'], 'data' => array_values($value['data'])];
				}
			}
		}
		$return = [];
		$return['title'] = $title;
		$return['categories'] = json_encode($categories, JSON_UNESCAPED_UNICODE);
		$return['series'] = json_encode($result, JSON_UNESCAPED_UNICODE);
		return $return;
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
			-- stat_poll::set_sarshomar_total_answered()
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
				-- stat_poll::set_sarshomar_total_answered()

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
			-- stat_poll::get_sarshomar_total_answered()

		";
		$total = \lib\db::get($stat_query, 'count', true);
		return intval($total);
	}
}
?>