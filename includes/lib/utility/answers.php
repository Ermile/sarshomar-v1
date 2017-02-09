<?php
namespace lib\utility;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

class answers
{
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
	 * check access to answer or no
	 *
	 * @param      array   $_args  The arguments
	 * @param      <type>  $_type  The type
	 */
	public static function access_answer($_args = [], $_type)
	{
		$default_args =
		[
			'user_id' => null,
			'poll_id' => null,
			'answer'  => [],
			'options' => [],
			'skipped' => false,
			'port'    => 'site',
			'subport' => null,
			'debug'	  => true,
		];
		$_args = array_merge($default_args, $_args);

		switch ($_type)
		{
			case 'add':
				if(self::is_answered($_args['user_id'], $_args['poll_id']))
				{
					if($_args['debug'])
					{
						debug::error(T_("You answered to this poll before"), 'answer', 'permission');
					}
					return false;
				}
				break;

			case 'edit':
				return self::check_access($_args, 'edit');
				break;

			case 'delete':
				return self::check_access($_args, 'delete');
				break;

			case 'check':
				$avalible = [];

				if(!self::is_answered($_args['user_id'], $_args['poll_id']))
				{
					array_push($avalible, 'add');
				}
				else
				{
					if(self::check_access($_args, 'edit'))
					{
						array_push($avalible, 'edit');
					}

					if(self::check_access($_args, 'delete'))
					{
						array_push($avalible, 'delete');
					}
				}
				return $avalible;

				break;

			default:
				debug::error(T_("Invalid type"), 'db', 'system');
				return false;
				break;
		}
		return true;

	}

	/**
	 * save poll answer
	 *
	 * @param      array   $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function save($_args = [])
	{
		$default_args =
		[
			'user_id' => null,
			'poll_id' => null,
			'answer'  => [],
			'options' => [],
			'skipped' => false,
			'port'    => 'site',
			'subport' => null,
		];
		$_args = array_merge($default_args, $_args);

		// check user status to set the chart 'valid' or 'invalid'
		$validation  = 'invalid';
		$user_validstatus = \lib\db\users::get($_args['user_id'], 'user_validstatus');
		if($user_validstatus && ($user_validstatus === 'valid' || $user_validstatus === 'invalid'))
		{
			$validation = $user_validstatus;
		}

		if(!is_array($_args['answer']))
		{
			$_args['answer'] = [$_args['answer']];
		}

		$access = self::access_answer($_args, 'add');

		if(!$access)
		{
			return;
		}

		$skipped = false;
		if($_args['skipped'] == true)
		{
			$skipped = true;
		}

		foreach ($_args['answer'] as $key => $value)
		{
			$set_option =
			[
				'answer_txt' => $value,
				'validation' => $validation,
				'port'       => $_args['port'],
				'subport'    => $_args['subport'],
			];

			$result = \lib\db\polldetails::save($_args['user_id'], $_args['poll_id'], $key, $set_option);
			// save the poll lucked by profile
			// update users profile
			$answers_details =
			[
				'validation'  => $validation,
				'poll_id'     => $_args['poll_id'],
				'opt_key'     => $key,
				'user_id'     => $_args['user_id'],
				'update_mode' => false,
			];

			\lib\utility\stat_polls::set_poll_result($answers_details);
			// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			// $update_profile = \lib\utility\profiles::set_profile_by_poll($answers_details);
			// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		}


		// set dashboard data
		if($skipped)
		{
			/**
			 * plus the ranks
			 * skip mod
			 */
			\lib\db\ranks::plus($_args['poll_id'], 'skip');

			\lib\utility\profiles::set_dashboard_data($_args['user_id'], "poll_skipped");
			\lib\utility\profiles::people_see_my_poll($_args['user_id'], $_args['poll_id'], "skipped");
		}
		else
		{
			/**
			 * plus the ranks
			 * vot mod
			 */
			\lib\db\ranks::plus($_args['poll_id'], 'vot');

			\lib\utility\profiles::set_dashboard_data($_args['user_id'], "poll_answered");
			\lib\utility\profiles::people_see_my_poll($_args['user_id'], $_args['poll_id'], "answered");
		}


		if(\lib\debug::$status)
		{
			return debug::true(T_("Answer Save"));
		}
		else
		{
			return debug::error(T_("Error in save your answer"));
		}
	}


	/**
	 * check access to answer to this poll
	 *
	 * @param      array   $_args  The arguments
	 * @param      <type>  $_type  The type
	 */
	public static function check_access($_args = [], $_type)
	{
		$default_args =
		[
			'user_id' => null,
			'poll_id' => null,
			'answer'  => [],
			'options' => [],
			'skipped' => false,
			'port'    => 'site',
			'subport' => null,
			'debug'   => true,
			'execute' => false,
		];

		$_args = array_merge($default_args, $_args);

			// get the old answered user to this poll
		$old_answer = \lib\db\polldetails::get($_args['user_id'], $_args['poll_id']);

		if(!$old_answer || !is_array($old_answer))
		{
			if($_type === 'edit' || $_type === 'delete')
			{
				if($_args['debug'])
				{
					debug::error(T_("You was not answer to this poll"), 'answer', 'permission');
				}
				return false;
			}
		}

		$must_remove = [];
		$must_insert = [];

		$old_opt =  array_column($old_answer, 'opt');
		$new_opt = array_keys($_args['answer']);
		if($old_opt == $new_opt)
		{
			if($_type === 'edit')
			{
				if($_args['debug'])
				{
					debug::error(T_("Duplicate answer, needless to update"), 'answer', 'permission');
				}
				return false;
			}
		}

		return debug::error(T_("Duplicate answer, needless to update"), 'answer', 'permission');
		var_dump($_args);
		var_dump($old_opt);
		var_dump($new_opt);
		var_dump($old_answer);
		exit();
		// make a array similar the answer array
		foreach ($opt_list as $key => $value)
		{
			if($value)
			{
				$opt_list[$key] = (int) $value;
			}
			else
			{
				$opt_list[$key] = $value;
			}
		}
		$must_remove = array_diff($opt_list, $_args['answer']);
		$must_insert = array_diff($_args['answer'], $opt_list);
		return [$must_remove, $must_insert, $old_answer];
		switch ($_type)
		{
			case 'value':
				# code...
				break;

			default:
				# code...
				break;
		}
	}


	/**
	 * check last user update
	 * the user can edit the answer for one min and 3 times
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      <type>  $_args['answer']   The answer
	 * @param      array   $_option   The option
	 */
	public static function recently_answered($_args = [])
	{

		$default_args =
		[
			'user_id' => null,
			'poll_id' => null,
			'answer'  => [],
			'options' => [],
			'skipped' => false,
			'port'    => 'site',
			'subport' => null,
			'time'    => 60,
			'count'   => 3
		];
		$_args = array_merge($default_args, $_args);


		list($must_remove, $must_insert, $old_answer) = self::analyze($_args);

		if($must_remove == $must_insert)
		{
			return debug::error(T_("Duplicate answer needless to update"), 'answer', 'arguments');
		}


		// default insert date
		$insert_time = date("Y-m-d H:i:s");
		$now         = time();
		foreach ($old_answer as $key => $value)
		{
			if(isset($value['insertdate']))
			{
				$insert_time = $value['insertdate'];
			}
			break;
		}

		$insert_time  = strtotime($insert_time);
		$diff_seconds = $now - $insert_time;

		if($diff_seconds > $_args['time'])
		{
			return debug::error(T_("Many time left, can not update answer"), 'answer', 'permission');
		}

		// get count of updated the poll
		$where =
		[
			'post_id'    => $_args['poll_id'],
			'user_id'    => $_args['user_id'],
			'option_cat' => "update_user_$_args[user_id]",
			'option_key' => "update_result_$_args[poll_id]",
		];
		if($_args['answer'] !== [])
		{
			\lib\db\options::plus($where);
		}

		$where['limit'] = 1;

		$update_count = \lib\db\options::get($where);

		if(!$update_count || !is_array($update_count) || !isset($update_count['value']))
		{
			return true;
		}

		$update_count = intval($update_count['value']);
		if($update_count > $_args['count'])
		{
			return debug::error(T_("Many update what are you doing?"),'answer', 'permission');
		}
		return true;
	}


	/**
	 * update the user answer
	 *
	 * @param      <type>  $_args['user_id']  The user identifier
	 * @param      <type>  $_args['poll_id']  The poll identifier
	 * @param      <type>  $_args['answer']   The answer
	 * @param      array   $_option   The option
	 */
	public static function update($_args = [])
	{
		$default_args =
		[
			'user_id' => null,
			'poll_id' => null,
			'answer'  => [],
			'options' => [],
			'skipped' => false,
			'port'    => 'site',
			'subport' => null,
		];
		$_args = array_merge($default_args, $_args);

		// check old answer and new answer and remove some record if need or insert record if need
		// or the old answer = new answer the return true
		// or old answer is skipped and new is a opt must be update
		// or old answer is a opt and now the user skipped the poll

		// when update the polldetails neet to update the pollstats
		// on this process we check the old answer and new answer
		// and update pollstats if need
		if(!self::check_access($_args, 'edit'))
		{
			return;
		}

		// remove answer must be remove
		foreach ($must_remove as $key => $value)
		{
			$remove_old_answer = \lib\db\polldetails::remove($_args['user_id'], $_args['poll_id'], $value);

			$profile    = 0;
			$validation = 'invalid';

			foreach ($old_answer as $i => $o)
			{
				if($o['opt'] == $value)
				{
					$profile    = $o['profile'];
					$validation = $o['validstatus'];
				}
			}

			$answers_details =
			[
				'poll_id'    => $_args['poll_id'],
				'opt_key'    => $value,
				'user_id'    => $_args['user_id'],
				'type'       => 'minus',
				'profile'    => $profile,
				'validation' => $validation
			];
			\lib\utility\stat_polls::set_poll_result($answers_details);
		}

		if(!empty($must_insert))
		{
			$_args['options']['in_update']   = true;
			$_args['options']['update_mode'] = true;
			self::save($_args);
		}

		return debug::true(T_("Your answer updated"));
	}


	/**
	 * change the user validation
	 * the user was in 'awaiting' status and
	 * we save all answers of this user in 'invalid' type of poll stats
	 * now the user active her account
	 * we change all stats the user was answered to it to 'valid' status
	 *
	 * @param      <type>  $_args['user_id']  The user identifier
	 */
	public static function change_user_validation($_user_id)
	{
		// get all user answer to poll
		$invalid_answers = \lib\db\polldetails::get($_user_id);

		foreach ($invalid_answers as $key => $value)
		{
			// check validstatus
			// we just update invalid answers to valid mod
			if($value['validstatus'] === 'invalid')
			{
				// opt = 0 means the user skipped the poll and neddless to update chart
				// opt = null means the user answers the other text (descriptive mode) needless to update chart
				if($value['opt'] !== 0 && $value['opt'] != null)
				{
					// plus the valid answers
					$plus_valid_chart =
					[
						'validation'  => 'valid',
						'update_mode' => true,
						'poll_id'     => $value['post_id'],
						'opt_key'     => $value['opt'],
						'user_id'     => $_user_id
					];

					\lib\utility\stat_polls::set_poll_result($plus_valid_chart);
					// minus the invalid answers
					$minus_invalid_chart =
					[
						'poll_id'    => $value['post_id'],
						'opt_key'    => $value['opt'],
						'user_id'    => $_user_id,
						'profile'    => $value['profile'],
						'type'       => 'minus',
						'validation' => 'invalid'
					];
					\lib\utility\stat_polls::set_poll_result($minus_invalid_chart);
				}
			}
			$query = "UPDATE polldetails SET validstatus = 'valid' WHERE user_id = $_user_id";
			return \lib\db::query($query);
		}
	}
}
?>