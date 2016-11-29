<?php
namespace lib\utility;

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
		// get the poll id
		if(isset($_args['poll_id']))
		{
			$poll_id = $_args['poll_id'];
		}
		else
		{
			return false;
		}

		// get the user id
		if(isset($_args['user_id']))
		{
			$user_id = $_args['user_id'];
		}
		else
		{
			return false;
		}

		// default of chart is not sorting poll
		$sorting  = false;
		// key = the opt_key and value = the sort index
		$sort_opt = [];
		// check the opt keys
		if(isset($_args['opt_key']))
		{
			// is array opt key mean we be in sorting mode
			if(is_array($_args['opt_key']))
			{
				// example of $_args[opt_key] : [1,2,3,4,5] || [5,4,3,2,1] the sorting mode
				$sorting = true;
				foreach ($_args['opt_key'] as $key => $value)
				{
					$sort_opt['opt_'. $value] = count($_args['opt_key']) - $key;
				}
				// example of $sort_opt =
				// [
				// 	opt_1 => 5,
				// 	opt_2 => 4,
				// 	opt_3 => 3,
				// 	opt_4 => 2,
				// 	opt_5 => 1
				// ];
			}
			// default poll and not sorting mode
			else
			{
				$opt_key = 'opt_'. $_args['opt_key'];
			}
		}
		else
		{
			return false;
		}

		// check the opt_text
		$opt_txt = null;
		if(isset($_args['opt_txt']))
		{
			$opt_txt = $_args['opt_txt'];
		}

		// default mode is plus the chart
		$plus = true;
		// check the type of change chart : plus | minus the chart
		// in sorting mode we have not minus type of change chart
		if(isset($_args['type']) && $_args['type'] != 'plus')
		{
			$plus = false;
		}

		// default port of user answer is 'site'
		$port = "'site'";
		if(isset($_args['port']))
		{
			$port = "'". $_args['port']. "'";
		}

		// default subport of the user answer is NULL, this method use in telegram mode
		$subport = "NULL";
		if(isset($_args['subport']))
		{
			$subport = "'". $_args['subport']. "'";
		}

		/**
		 * set count total answere + 1
		 * to get sarshomar total answered
		 * in the minus mode we not change the sarshomar total answered
		 */
		if($plus)
		{
			self::set_sarshomar_total_answered();
		}

		// user skip the poll
		// neelless to change the chart
		// and this check must be after set sarshomar_total_answered
		// becaus the user see the poll and answer to this
		// but the answer of this user needless to change the chart
		if($opt_key == "opt_0")
		{
			return true;
		}

		// the user profile data to make chart by this items
		$user_profile_data = [];

		// in minus mode we set the profile
		// and we shuld not get the current user profile
		// we get the profile of users has been answered by this profile
		// and load old profile data to minus the chart
		if(isset($_args['profile']))
		{
			// get profile data in filter table
			$user_profile_data = \lib\db\filters::get($_args['profile']);
			if(is_array($user_profile_data))
			{
				// remove empty value from profile to minus the 'undefined' of chart
				$user_profile_data = array_filter($user_profile_data);
			}
		}
		// the profile not set
		// we get the current profile data of users
		else
		{
			// get the current profile data of users
			$user_profile_data = \lib\utility\profiles::get_profile_data($user_id);
		}
		// get the support filter of service
		// some index of profile data we have not eny chart of this
		// we have the chart of all index in filters::support_filter()
	    $support_filter = \lib\db\filters::support_filter();

	    // the keys of support_filter is important
	    // the value of this array use in other place
	    $support_filter = array_keys($support_filter);

	    // get the poll stats record to open the chart and change it
		$pollstats = \lib\db\pollstats::get($poll_id);
		// if the poll stats record is find
		// we must be change the chart
		// and when the poll stats not found we must creat the chart
		if($pollstats && is_array($pollstats))
		{
			// set the update mode to run update query
			$update_mode = true;
			$pollstats   = $pollstats;
		}
		else
		{
			// set the insert mod to run insert query
			$update_mode = false;
			$pollstats   = [];
		}

		// update mode
		// we update the chart
		$set = [];
		// plus the total answered of this poll
		if(isset($pollstats['total']) && $pollstats['total'])
		{
			// in plus mode we ++ the total answered to this poll
			// in minus mode we not change the total field
			if($plus)
			{
				$pollstats['total']++;
			}
		}
		// first times to set the total fields
		else
		{
			$pollstats['total'] = 1;
		}
		// set the pollstats.total field in query
		$set[] = " pollstats.total = ". $pollstats['total'];

		// if we in sorting mode:
		// update all opt of this poll
		// all opt of this poll was plused by sort index value
		if($sorting)
		{
			// update all index of opt of this poll
			foreach ($sort_opt as $opt => $sort_index)
			{
				if(isset($pollstats['result'][$opt]))
				{
					$pollstats['result'][$opt] += $sort_index;
				}
				else
				{
					$pollstats['result'][$opt] = $sort_index;
				}
			}
		}
			 // we not in sorting mode
		else //
		     // we plus one opt of this poll
		{
			if(isset($pollstats['result'][$opt_key]))
			{
				if($plus)
				{
					$pollstats['result'][$opt_key]++;
				}
				else
				{
					if(intval($pollstats['result'][$opt_key]) > 0)
					{
						$pollstats['result'][$opt_key]--;
					}
				}
			}
			else
			{
				if($plus)
				{
					$pollstats['result'][$opt_key] = 1;
				}
			}
		}
		// update the result field in table
		$set[] = " pollstats.result = '". json_encode($pollstats['result'], JSON_UNESCAPED_UNICODE). "'";

		// for each support filter do this:
		foreach ($support_filter as $key => $filter)
		{
			// check the user have this filter or no
			// if the users have this filter:
			if(isset($user_profile_data[$filter]))
			{
				// if in sorting mode we update all opt index of this poll
				if($sorting)
				{
					// update all opt index of this poll
					foreach ($sort_opt as $opt => $sort_index)
					{
						if(isset($pollstats[$filter][$opt]))
						{
							if(isset($pollstats[$filter][$opt][$user_profile_data[$filter]]))
							{
								$pollstats[$filter][$opt][$user_profile_data[$filter]]+= $sort_index;
							}
							else
							{
								$pollstats[$filter][$opt][$user_profile_data[$filter]] = $sort_index;
							}
						}
						else
						{
							$pollstats[$filter][$opt][$user_profile_data[$filter]] = $sort_index;
						}
					}
				}
					 // we not in sorting mode
				else // update one opt of this poll
				     //
				{
					// check the filter of this opt
					if(isset($pollstats[$filter][$opt_key]))
					{
						if(isset($pollstats[$filter][$opt_key][$user_profile_data[$filter]]))
						{
							if($plus)
							{
								$pollstats[$filter][$opt_key][$user_profile_data[$filter]]++;
							}
							else
							{
								if(intval($pollstats[$filter][$opt_key][$user_profile_data[$filter]]) > 1)
								{
									$pollstats[$filter][$opt_key][$user_profile_data[$filter]]--;
								}
								else
								{
									unset($pollstats[$filter][$opt_key][$user_profile_data[$filter]]);
								}
							}
						}
						else
						{
							if($plus)
							{
								$pollstats[$filter][$opt_key][$user_profile_data[$filter]] = 1;
							}
						}
					}
					else
					{
						if($plus)
						{
							$pollstats[$filter][$opt_key][$user_profile_data[$filter]] = 1;
						}
					}
				}
			}
				 // the user not set this filter
			else //
			     // we set this item of chart as 'undefined'
			{
				// if in sorting mode we update all opt index of this poll
				if($sorting)
				{
					foreach ($sort_opt as $opt => $sort_index)
					{
						if(isset($pollstats[$filter][$opt]))
						{
							if(isset($pollstats[$filter][$opt]['undefined']))
							{

								$pollstats[$filter][$opt]['undefined']+= $sort_index;
							}
							else
							{
								$pollstats[$filter][$opt]['undefined'] = $sort_index;
							}
						}
						else
						{
							$pollstats[$filter][$opt]['undefined'] = $sort_index;
						}
					}
				}
				else // we not in sorting mode
				{
					if(isset($pollstats[$filter][$opt_key]))
					{
						if(isset($pollstats[$filter][$opt_key]['undefined']))
						{
							if($plus)
							{
								$pollstats[$filter][$opt_key]['undefined']++;
							}
							else
							{
								if(intval($pollstats[$filter][$opt_key]['undefined']) > 1)
								{
									$pollstats[$filter][$opt_key]['undefined']--;
								}
								else
								{
									unset($pollstats[$filter][$opt_key]['undefined']);
								}
							}
						}
						else
						{
							if($plus)
							{
								$pollstats[$filter][$opt_key]['undefined'] = 1;
							}
						}
					}
					else
					{
						if($plus)
						{
							$pollstats[$filter][$opt_key]['undefined'] = 1;
						}
					}
				}
			}

			$set[] = " pollstats.$filter = '". json_encode($pollstats[$filter], JSON_UNESCAPED_UNICODE). "'";

		} // end of foreach $support_filter

		//
		if($update_mode)
		{
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
		}
		else
		{
			// insert record
			$set[] =  " port = $port ";
			$set[] =  " subport = $subport ";
			$set[] =  " post_id = $poll_id ";

			$set = join($set, " , ");
			$query =
			"
				INSERT INTO
					pollstats
				SET
					$set
			";
			$set_result = \lib\db::query($query);
		}
		return true;
	}


	/**
	 * get result of specefic item
	 * @param  [type] $_poll_id [description]
	 * @param  [type] $_value   [description]
	 * @param  [type] $_key     [description]
	 * @return [type]           [description]
	 */
	public static function get_telegram_result($_poll_id)
	{
		// get answers form post meta
		$poll = \lib\db\polls::get_poll($_poll_id);
		$meta = $poll['meta'];

		$opt = $meta['opt'];

		$result = \lib\db\pollstats::get($_poll_id, ['field' => 'result']);
		if(isset($result['result']))
		{
			$answers = $result['result'];
		}
		else
		{
			return false;
		}

		if(!is_array($answers))
		{
			$answers = [$answers];
		}
		if(!is_array($opt))
		{
			return false;
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


	/**
	 * get result of specefic item
	 * @param  [type] $_poll_id  the poll id
	 * @param  [string|array|'*'] $_type the type of result
	 * 			leav blank to get public result
	 * 			set string of poll result for example 'gender' to get result of gender
	 * 			set array to get list of result
	 * 			set '*' to get all result
	 * @param  [type] $_mode     [description]
	 * @return [type]           [description]
	 */
	public static function get_result($_poll_id, $_type = null, $_mode = "highcharts")
	{
		// get poll meta to get all opt of this poll
		$poll = \lib\db\polls::get_poll($_poll_id);
		// we can not found meta of this poll
		if(!isset($poll['meta']) || empty($poll))
		{
			return false;
		}

		$poll_meta = $poll['meta'];
		// the opt key in the poll meta
		if(isset($poll_meta['opt']))
		{
			$poll_opt = $poll_meta['opt'];
		}
		else
		{
			return false;
		}

		$result = \lib\db\pollstats::get($_poll_id);

		// process result
		if($result)
		{
			if($_type)
			{
				// $_type is array . return list of result of this array
				if(is_array($_type))
				{
					$array_result = [];
					foreach ($_type as $key => $value) {
						// public result is different
						if($key == 'result')
						{
							$result_temp =  self::process_public_result($poll, $poll_opt, $result, 'result', $_mode);
						}
						else
						{
							$result_temp =  self::process_result($poll, $poll_opt, $result, $value);
							if($_mode == "highcharts")
							{
								$result_temp = self::high_charts_mod($result_temp);
							}
						}
						$array_result[$value] = $result_temp;
					}
					return $array_result;
				}
				// get all result
				elseif($_type == "*")
				{
					$array_result = [];
					foreach ($result as $key => $value) {
						// check key. some key is not result
						switch ($key) {
							case 'id':
							case 'post_id':
							case 'port':
							case 'subport':
							case 'total':
							case 'meta':
								continue;
								break;
							// public result is different
							case 'result':
								$array_result[$key] = self::process_public_result($poll, $poll_opt, $result, $key, $_mode);
								break;

							default:
								// chekc $_mode
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
				// return one of result
				elseif(is_string($_type))
				{
					return self::process_result($poll, $poll_opt, $result, $_type);
				}
			}
			else
			{
				// return public result
				return self::process_public_result($poll, $poll_opt, $result, 'result', $_mode);
			}
		}
		else
		{
			// no result set to this poll
			return null;
		}
	}


	/**
	 * progress public result
	 *
	 * @param      <type>  $_poll      The poll
	 * @param      <type>  $_poll_opt  The poll option
	 * @param      <type>  $_result    The result
	 * @param      <type>  $_type      The type
	 * @param      string  $_mode      The mode
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public static function process_public_result($_poll, $_poll_opt, $_result, $_type, $_mode)
	{
		$poll     = $_poll;
		$poll_opt = $_poll_opt;
		$result   = $_result;

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


	/**
	 * Gets the sarshomar total answered.
	 *
	 * @return     <type>  The sarshomar total answered.
	 */
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


	/**
	 * Gets the random poll id by tags #homepage
	 *
	 * @return     boolean  The random poll identifier.
	 */
	public static function get_random_poll_result()
	{
		$get_id = [];

		$query =
		"
			SELECT
				termusage_id AS 'id'
			FROM
				termusages
			INNER JOIN terms ON
				terms.id = termusages.term_id AND
				terms.term_type = 'tag' AND
				terms.term_slug = 'homepage'
			WHERE
				termusages.termusage_foreign = 'posts'
			ORDER BY RAND()
			LIMIT 1
			-- get random poll id by tag homepage to show in homepage
		";
		$random_poll_id = \lib\db::get($query, "id", true);
		$poll_result = self::get_result($random_poll_id);
		return $poll_result;
	}
}
?>