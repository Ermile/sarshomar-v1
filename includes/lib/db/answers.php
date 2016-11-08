<?php
namespace lib\db;

class answers
{
	/**
	 * insert answers to options table
	 *
	 * @param      array  $_args  list of answers and post id
	 *
	 * @return     <type>  mysql result
	 */
	public static function insert($_args){

		// set key of option table to sort answer
		// @example the poll have 3 answer
		// who we save this answers to table ?
		// [options table] : 	cat 				kye 		value  		 (the fields)
		// 						poll_(post_id)		opt_1		[answer 1]
		// 						poll_(post_id)		opt_1		[answer 1]
		// 						poll_(post_id)		opt_3		[answer 3]
		$answers   = [];
		$opt_meta = [];
		// answers key : opt_1, opt_2, opt_[$i], ...
		$i = 0;
		foreach ($_args['answers'] as $key => $value)
		{

			$meta = [
					'desc'  => '',
					'true'  => isset($value['true'])  ? $value['true']  : '',
					'point' => isset($value['point']) ? $value['point'] : '',
					'type'  => isset($value['type'])  ? $value['type']  : ''
					];

			// answers key : opt_1, opt_2, opt_[$i], ...
			$i++;
			$answers[] =
			[
				'post_id'      => $_args['poll_id'],
				'option_cat'   => 'poll_' . $_args['poll_id'],
				'option_key'   => 'opt_' .  $i,
				'option_value' => $value['txt'],
				'option_meta'  => json_encode($meta, JSON_UNESCAPED_UNICODE)
			];

			$opt_meta[] =
			[
				'key'  => 'opt_' .  $i,
				'txt'  => isset($value['txt'])  ? $value['txt']  : '',
				'type' => isset($value['type']) ? $value['type'] : ''
			];

		}

		$return = \lib\db\options::insert_multi($answers);

		// creat meta of options table for one answers record
		// every question have more than two json param.
		// opt : answers of this poll
		// answers : count of people answered to this poll
		// desc : description of answers
		$meta =
		[
			'opt'     	=> $opt_meta
		];

		// merge old meta and new meta in post meta
		$set_meta = \lib\db\polls::merge_meta($meta, $_args['poll_id']);
		return $return;
	}

	public static function update($_args, $_id)
	{
		return \lib\db\options::update($_args, $_id);
	}


	/**
	 * get post id and return opt of this post
	 *
	 * @param      <type>  $_poll_id  The post identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_poll_id)
	{
		$query = "
				SELECT
					*
				FROM
					options
				WHERE
					post_id = $_poll_id AND
					option_cat LIKE 'poll_{$_poll_id}' AND
					option_key LIKE 'opt%'  AND
					user_id IS NULL
				-- answers::get()
				";
		$result = \lib\db\options::select($query, "get");
		return \lib\utility\filter::meta_decode($result);
	}


	/**
	 * save user answer into options table
	 * @param  [type] $_user_id [description]
	 * @param  [type] $_poll_id [description]
	 * @param  [type] $_answer  [description]
	 * @return [type]           [description]
	 */
	public static function save($_user_id, $_poll_id, $_answer, $_answer_txt = null)
	{
		if(is_array($_answer))
		{
			foreach ($_answer as $key => $value)
			{
				if(substr($key, 0, 4) != 'opt_')
				{
					$key = 'opt_'. $key;
				}

				$num_of_opt_kye = explode('_', $key);
				$num_of_opt_kye = end($num_of_opt_kye);
				if(!$num_of_opt_kye)
				{
					continue;
				}
				$result = self::save_polldetails($_user_id, $_poll_id, $num_of_opt_kye, $value);
				// save the poll lucked by profile
				// update users profile
				$answers_details =
				[
					'poll_id' => $_poll_id,
					'opt_key' => $key,
					'user_id' => $_user_id
				];
				// save answered count
				if($key != 'opt_other')
				{
					\lib\db\stat_polls::set_poll_result($answers_details);
				}
			}
		}
		else
		{
			if(substr($_answer, 0, 4) != 'opt_')
			{
				$_answer = 'opt_'. $_answer;
			}
			$num_of_opt_kye = explode('_', $_answer);
			$num_of_opt_kye = end($num_of_opt_kye);
			$result = self::save_polldetails($_user_id, $_poll_id, $num_of_opt_kye, $_answer_txt);
			// save the poll lucked by profile
			// update users profile
			$answers_details =
			[
				'poll_id' => $_poll_id,
				'opt_key' => $_answer,
				'user_id' => $_user_id
			];
			// save answered count
			if($_answer != 'opt_other')
			{
				\lib\db\stat_polls::set_poll_result($answers_details);
			}
		}

		$update_profile = \lib\db\profiles::set_profile_by_poll($answers_details);

		// set dashboard data
		if($_answer == 'opt_0')
		{
			\lib\db\profiles::set_dashboard_data($_user_id, "poll_skipped");
			\lib\db\profiles::people_see_my_poll($_user_id, $_poll_id, "skipped");
		}
		else
		{
			\lib\db\profiles::set_dashboard_data($_user_id, "poll_answered");
			\lib\db\profiles::people_see_my_poll($_user_id, $_poll_id, "answered");
		}

		return \lib\debug::$status;
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function delete($_poll_id)
	{
		$query =
		"
			UPDATE
				options
			SET
				options.option_status = 'disable'
			WHERE
				options.post_id = $_poll_id AND
				options.option_key LIKE 'opt%' AND
				options.user_id IS NULL
			-- answers::delete()
		";
		return \lib\db::query($query);
	}


	/**
	 * check the user answered to this poll or no
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function is_answered($_user_id, $_poll_id)
	{
		$query =
		"
			SELECT
				id
			FROM
				polldetails
			WHERE
				user_id = $_user_id AND
				post_id = $_poll_id
			LIMIT 1
			-- answers::is_answered()
			-- check user is answered to this poll or no
		";
		$result = \lib\db::get($query, 'id', true);
		if($result)
		{
			return true;
		}
		return false;
	}


	/**
	 * Saves polldetails.
	 *
	 * @param      <type>  $_user_id        The user identifier
	 * @param      <type>  $_poll_id        The poll identifier
	 * @param      <type>  $num_of_opt_kye  The number of option kye
	 * @param      <type>  $_answer_txt     The answer text
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function save_polldetails($_user_id, $_poll_id, $num_of_opt_kye, $_answer_txt = null)
	{
		$insert_polldetails =
		"
			INSERT INTO
				polldetails
			SET
				user_id = $_user_id,
				post_id = $_poll_id,
				opt     = '$num_of_opt_kye',
				type    = (SELECT post_type FROM posts WHERE posts.id = $_poll_id LIMIT 1),
				txt     = '$_answer_txt',
				profile = (SELECT filter_id FROM users WHERE users.id = $_user_id LIMIT 1),
				visitor_id = NULL
				-- answers::save()
		";
		$result = \lib\db::query($insert_polldetails);
		return $result;
	}
}
?>