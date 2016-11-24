<?php
namespace lib\utility;

class answers
{
	/**
	 * insert answers to options table
	 *
	 * @param      array  $_args  list of answers and post id
	 *
	 * @return     <type>  mysql result
	 */
	public static function insert($_args)
	{

		// set key of option table to sort answer
		// @example the poll have 3 answer
		// who we save this answers to table ?
		// [options table] : 	cat 				kye 		value  		 (the fields)
		// 						poll_(post_id)		opt_1		[answer 1]
		// 						poll_(post_id)		opt_1		[answer 1]
		// 						poll_(post_id)		opt_3		[answer 3]
		// $_args =
		// [
		// 	'poll_id' => 1,
		// 	'answers' =>
		// 		[
		// 			'txt' => 'answer one',
		// 			'type' => 'audio',
		// 			'desc' => 'description',
		// 			'true' => 'true|false',
		// 			'point' => 10
		// 		],
		// 		[
		// 			'txt' => 'answer two',
		// 			'type' => 'audio',
		// 			'desc' => 'description',
		// 			'true' => 'true|false',
		// 			'point' => 10
		// 		]
		// 	];
		$answers   = [];
		$opt_meta = [];
		// answers key : opt_1, opt_2, opt_[$i], ...
		$i = 0;
		foreach ($_args['answers'] as $key => $value)
		{

			$meta = [
					'desc'  => isset($value['desc'])  ? $value['desc']  : '',
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
				'key'  => 'opt_'.  $i,
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
		$meta = ['opt' 	=> $opt_meta];

		// merge old meta and new meta in post meta
		$set_meta = \lib\db\polls::merge_meta($meta, $_args['poll_id']);
		return $return;
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
					user_id IS NULL AND
					option_status = 'enable'
				-- answers::get()
				";
		$result = \lib\db\options::select($query, "get");
		return \lib\utility\filter::meta_decode($result);
	}


	/**
	 * return the status array
	 *
	 * @param      <type>   $_status  The status
	 * @param      boolean  $_update  The update
	 * @param      array    $_msg     The message
	 */
	public static function status($_status, $_opt, $_msg = null)
	{
		$opt_index = null;
		if(is_string($_opt))
		{
			$opt_index = explode("_", $_opt);
			$opt_index = end($opt_index);
		}
		return
		[
			'status'    => $_status,
			'opt'       => $_opt,
			'opt_index' => $opt_index,
			'msg'       => $_msg
		];
	}


	/**
	 * save user answer into options table
	 * @param  [type] $_user_id [description]
	 * @param  [type] $_poll_id [description]
	 * @param  [type] $_answer  [description]
	 * @return [type]           [description]
	 */
	public static function save($_user_id, $_poll_id, $_answer, $_option = [])
	{
		// check poll status
		$status = \lib\db\polls::get_poll_status($_poll_id);
		if($status != 'publish')
		{
			return self::status(false, null, T_("poll is not published"));
		}

		$in_update = false;
		if(isset($_option['in_update']) && $_option['in_update'])
		{
			$in_update = true;
		}
		unset($_option['in_update']);

		// if we not in update mod we need to check user answer
		// but in update mod we need to save the user answer whitout check old answer
		// the old answer was check in self::update()

		if(!$in_update)
		{
			// cehck is answer to this poll or no
			$is_answered = self::is_answered($_user_id, $_poll_id);
			if($is_answered)
			{
				if(\lib\db\polls::check_meta($_poll_id, "update_result"))
				{
					return self::update(...func_get_args());
				}
				return self::status(false, $is_answered, T_("poll can not update result"));
			}
		}

		$skipped = false;
		$default_option =
		[
			'answer_txt' => null,
			'port'       => 'site',
			'subport'    => null
		];
		$_option = array_merge($default_option, $_option);

		if(is_array($_answer))
		{
			foreach ($_answer as $key => $value)
			{
				if(substr($key, 0, 4) != 'opt_')
				{
					$key = 'opt_'. $key;
				}

				// to save dashoboard data
				if($key == 'opt_0')
				{
					$skipped = true;
				}

				$num_of_opt_kye = explode('_', $key);
				$num_of_opt_kye = end($num_of_opt_kye);
				if(!$num_of_opt_kye && $num_of_opt_kye !== '0')
				{
					continue;
				}

				$set_option = ['answer_txt' => $value];
				$set_option = array_merge($_option, $set_option);
				$result = self::save_polldetails($_user_id, $_poll_id, $num_of_opt_kye, $set_option);
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
					\lib\utility\stat_polls::set_poll_result($answers_details);
				}
			}
		}
		else
		{
			$num_of_opt_kye = null;

			if(substr($_answer, 0, 4) !== 'opt_')
			{
				$num_of_opt_kye = $_answer;
				$_answer = 'opt_'. $_answer;
			}

			// to save dashboard data
			if($_answer == 'opt_0')
			{
				$skipped = true;
			}

			if(!$num_of_opt_kye)
			{
				$num_of_opt_kye = explode('_', $_answer);
				$num_of_opt_kye = end($num_of_opt_kye);
			}

			$result = self::save_polldetails($_user_id, $_poll_id, $num_of_opt_kye, $_option);
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
				\lib\utility\stat_polls::set_poll_result($answers_details);
			}
			$update_profile = \lib\utility\profiles::set_profile_by_poll($answers_details);
		}

		if(!$in_update)
		{
			// set dashboard data
			if($skipped)
			{
				\lib\utility\profiles::set_dashboard_data($_user_id, "poll_skipped");
				\lib\utility\profiles::people_see_my_poll($_user_id, $_poll_id, "skipped");
			}
			else
			{
				\lib\utility\profiles::set_dashboard_data($_user_id, "poll_answered");
				\lib\utility\profiles::people_see_my_poll($_user_id, $_poll_id, "answered");
			}
		}

		if(\lib\debug::$status)
		{
			return self::status(true, $_answer, T_("answer save"));
		}
		else
		{
			return self::status(false, null, T_("error in save your answer"));
		}
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
				*
			FROM
				polldetails
			WHERE
				user_id = $_user_id AND
				post_id = $_poll_id
			LIMIT 1
			-- answers::is_answered()
			-- check user is answered to this poll or no
		";
		$result = \lib\db::get($query, null, true);
		if($result)
		{
			return $result;
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
	public static function save_polldetails($_user_id, $_poll_id, $num_of_opt_kye, $_option = [])
	{
		$default_option =
		[
			'answer_txt' => null,
			'port'       => 'site',
			'subport'    => null
		];
		$_option = array_merge($default_option, $_option);

		if($_option['port'] == null)
		{
			$port = "NULL";
		}
		else
		{
			$port = "'$_option[port]'";
		}

		if($_option['subport'] == null)
		{
			$subport = "NULL";
		}
		else
		{
			$subport = "'$_option[subport]'";
		}

		$insert_polldetails =
		"
			INSERT INTO
				polldetails
			SET
				user_id = $_user_id,
				post_id = $_poll_id,
				port    = $port,
				subport = $subport,
				opt     = '$num_of_opt_kye',
				type    = (SELECT post_type FROM posts WHERE posts.id = $_poll_id LIMIT 1),
				txt     = '$_option[answer_txt]',
				profile = (SELECT filter_id FROM users WHERE users.id = $_user_id LIMIT 1),
				visitor_id = NULL
				-- answers::save_polldetails()
		";
		$result = \lib\db::query($insert_polldetails);
		return $result;
	}


	/**
	 * remove user answered to poll
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function remove($_user_id, $_poll_id, $_opt_index = null)
	{
		if($_opt_index === null)
		{
			$opt = null;
		}
		else
		{
			$opt = " AND opt = '$_opt_index' ";
		}

		$query =
		"
			DELETE FROM
				polldetails
			WHERE
				user_id = $_user_id AND
				post_id = $_poll_id
				$opt
		";
		return \lib\db::query($query);
	}


	/**
	 * update the user answer
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      <type>  $_answer   The answer
	 * @param      array   $_option   The option
	 */
	public static function update($_user_id, $_poll_id, $_answer, $_option = [])
	{
		// check old answer and new answer and remove some record if need or insert record if need
		// or the old answer = new answer the return true
		// or old answer is skipped and new is a opt must be update
		// or old answer is a opt and now the user skipped the poll

		// when update the polldetails neet to update the pollstats
		// on this process we check the old answer and new answer
		// and update pollstats if need

		// get the old answered user to this poll
		$old_answer = \lib\db\polldetails::get($_user_id, $_poll_id);

		if(is_array($old_answer) && empty($old_answer))
		{
			// the user not answered to this poll
			// we save the user answer
			return self::save(...func_get_args());
		}

		// make a array similar the answer array
		$opt_list =  array_column($old_answer, 'opt', 'txt');
		foreach ($opt_list as $key => $value)
		{
			$opt_list[$key] = "opt_". $value;
		}
		$opt_list    = array_flip($opt_list);
		$must_remove = array_diff($opt_list, $_answer);
		$must_insert = array_diff($_answer, $opt_list);

		// remove answer must be remove
		foreach ($must_remove as $key => $value)
		{
			$opt_index = explode("_", $key);
			$opt_index = end($opt_index);
			$remove_old_answer = self::remove($_user_id, $_poll_id, $opt_index);

			$profile = 0;
			foreach ($old_answer as $i => $o)
			{
				if($o['opt'] == $opt_index)
				{
					$profile = $o['profile'];
				}
			}

			$answers_details =
			[
				'poll_id' => $_poll_id,
				'opt_key' => $key,
				'user_id' => $_user_id,
				'type'    => 'minus',
				'profile' => $profile
			];
			\lib\utility\stat_polls::set_poll_result($answers_details);
		}

		foreach ($must_insert as $key => $value)
		{
			self::save($_user_id, $_poll_id, [$key => $value], ['in_update' => true]);
			// set the poll stat in save function
		}
		return self::status(true, $_answer, T_("poll answre updated"));
	}
}
?>