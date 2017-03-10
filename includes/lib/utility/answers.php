<?php
namespace lib\utility;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

class answers
{
	public static $old_answer;
	public static $must_insert;
	public static $must_remove;

	public static $validation  = 'invalid';
	public static $user_verify = null;

	public static $IS_ANSWERED = [];

	/**
	 * check the user answered to this poll or no
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function is_answered($_user_id, $_poll_id, $_options = [])
	{
		if(!$_user_id || !$_poll_id)
		{
			// debug::error(T_("User id or poll id not found"), 'is_answered', 'db');
			return false;
		}

		if(!isset(self::$IS_ANSWERED[$_user_id][$_poll_id]))
		{
			$query =
			"
				SELECT
					*
				FROM
					polldetails
				WHERE
					polldetails.user_id = $_user_id AND
					polldetails.post_id = $_poll_id
				-- to get enable at first
				ORDER BY polldetails.status ASC
				-- answers::is_answered()
			";
			$result = \lib\db::get($query, null);
			self::$IS_ANSWERED[$_user_id][$_poll_id] = $result;
		}

		$default_options =
		[
			'type' => false,
		];
		$_options = array_merge($default_options, $_options);

		if($_options['type'] === 'all')
		{
			if(isset(self::$IS_ANSWERED[$_user_id][$_poll_id]))
			{
				return self::$IS_ANSWERED[$_user_id][$_poll_id];
			}
			return false;
		}

		if(isset(self::$IS_ANSWERED[$_user_id][$_poll_id]) && is_array(self::$IS_ANSWERED[$_user_id][$_poll_id]))
		{
			$temp = [];
			foreach (self::$IS_ANSWERED[$_user_id][$_poll_id] as $key => $value)
			{
				if(isset($value['status']) && $value['status'] == 'enable')
				{
					array_push($temp, $value);
				}
			}

			if(empty($temp))
			{
				return false;
			}
			return $temp;
		}
		return false;
	}


	/**
	 * check access to answer to this poll
	 *
	 * @param      array   $_args  The arguments
	 */
	public static function check_time_count($_args = [])
	{

		$default_args =
		[
			'time'    => (60 * 7),
			'count'   => 3,
			'user_id' => null,
			'poll_id' => null,
			'debug'   => true,
		];

		if(defined('Tld') && Tld === 'dev')
		{
			$default_args['time']  = 60 * 60 * 24 * 365; // 1 year
			$default_args['count'] = 3 * 100; // 300 times
		}

		$_args = array_merge($default_args, $_args);

		// get the old answered user to this poll
		self::$old_answer = self::is_answered($_args['user_id'], $_args['poll_id']);

		if(!self::$old_answer || !is_array(self::$old_answer))
		{
			return true;
		}

		$insert_time = date("Y-m-d H:i:s");
		$now         = time();
		foreach (self::$old_answer as $key => $value)
		{
			if(isset($value['insertdate']))
			{
				$insert_time = $value['insertdate'];
				break;
			}
		}

		$insert_time  = strtotime($insert_time);
		$diff_seconds = $now - $insert_time;

		if($diff_seconds > $_args['time'])
		{
			if($_args['debug'])
			{
				debug::error(T_("Many time left, can not update or delete answer"), 'answer', 'permission');
			}
			return false;
		}

		// get count of updated the poll
		$where =
		[
			'post_id'    => $_args['poll_id'],
			'user_id'    => $_args['user_id'],
			'option_cat' => "update_user_$_args[user_id]",
			'option_key' => "update_result_$_args[poll_id]",
			'limit'      => 1,
		];

		$update_count = \lib\db\options::get($where);

		if(isset($update_count['value']))
		{
			if((int) $update_count > (int) $_args['count'])
			{
				if($_args['debug'])
				{
					debug::error(T_("You have updated your answer many times and can not update it anymore"),'answer', 'permission');
				}
				return false;
			}
		}
		return true;
	}


	/**
	 * check access to answer or no
	 *
	 * @param      array   $_args  The arguments
	 * @param      <type>  $_type  The type
	 */
	public static function access_answer($_args = [], $_type)
	{

		if(!is_array($_args))
		{
			$_args = [];
		}

		$default_args =
		[
			'user_id' => null,
			'poll_id' => null,
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
						debug::error(T_("You have already answered to this poll"), 'answer', 'permission');
					}
					return false;
				}
				break;

			case 'edit':
			case 'delete':
				return self::check_time_count($_args);
				break;

			case 'check':
				$avalible = [];

				if(!self::is_answered($_args['user_id'], $_args['poll_id']))
				{
					array_push($avalible, 'add');
					array_push($avalible, 'skip');
				}
				else
				{
					if(self::check_time_count($_args))
					{
						array_push($avalible, 'edit');
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
	 * check user verify
	 * set self::$validation
	 * return true or false
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function user_validataion($_user_id)
	{
		$save_offline_chart = true;

		self::$user_verify = \lib\db\users::get($_user_id, 'user_verify');

		switch (self::$user_verify)
		{
			case 'complete':
			case 'mobile':
				self::$validation  = 'valid';
				break;

			case 'uniqueid':
				self::$validation  = 'invalid';
				break;

			case 'unknown':
			default:
				$save_offline_chart = false;
				break;
		}
		return $save_offline_chart;
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

		/**
		 * save offline chart
		 * in guest mod we needless to save offline chart
		 *
		 * @var        boolean
		 */
		$save_offline_chart = self::user_validataion($_args['user_id']);


		if(!is_array($_args['answer']))
		{
			$_args['answer'] = [$_args['answer']];
		}

		$access = self::access_answer($_args, 'add');

		if(!$access)
		{
			return;
		}

		$user_delete_answer = self::is_answered($_args['user_id'], $_args['poll_id'], ['type' =>  'all']);

		$set_option =
		[
			'answer_txt' => null,
			'validation' => self::$validation,
			'port'       => $_args['port'],
			'subport'    => $_args['subport'],
		];

		$skipped = false;

		if($_args['skipped'] == true)
		{
			$skipped = true;
			$result  = \lib\db\polldetails::save($_args['user_id'], $_args['poll_id'], 0, $set_option);
		}
		else
		{
			foreach ($_args['answer'] as $key => $value)
			{
				$set_option =
				[
					'answer_txt'  => $value,
					'validation'  => self::$validation,
					'user_verify' => self::$user_verify,
					'port'        => $_args['port'],
					'subport'     => $_args['subport'],
				];

				$result = \lib\db\polldetails::save($_args['user_id'], $_args['poll_id'], $key, $set_option);
				// save the poll lucked by profile
				// update users profile
				$answers_details =
				[
					'validation'  => self::$validation,
					'user_verify' => self::$user_verify,
					'poll_id'     => $_args['poll_id'],
					'opt_key'     => $key,
					'user_id'     => $_args['user_id'],
					'update_mode' => false,
					'port'        => $_args['port'],
					'subport'     => $_args['subport'],
				];
				// save user profile if this poll is a profile poll
				\lib\utility\profiles::set_profile_by_poll($answers_details);

				if($save_offline_chart)
				{
					\lib\utility\stat_polls::set_poll_result($answers_details);
				}
			}
		}

		/**
		 * set count total answere + 1
		 * to get sarshomar total answered
		 * in the minus mode we not change the sarshomar total answered
		 */
		if(!$user_delete_answer)
		{
			\lib\utility\stat_polls::set_sarshomar_total_answered();
		}

		// set offline data
		if($skipped)
		{
			\lib\db\ranks::plus($_args['poll_id'], 'skip');
			\lib\utility\profiles::set_dashboard_data($_args['user_id'], "poll_skipped");
			\lib\utility\profiles::people_see_my_poll($_args['poll_id'], "skipped", 'plus');
		}
		else
		{
			\lib\db\ranks::plus($_args['poll_id'], 'vote');
			\lib\utility\profiles::set_dashboard_data($_args['user_id'], "poll_answered");
			\lib\utility\profiles::people_see_my_poll($_args['poll_id'], "answered", 'plus');
		}

		self::$IS_ANSWERED = [];

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

		if(!self::access_answer($_args, 'edit'))
		{
			return;
		}

		$old_opt = [];

		self::$old_answer = \lib\db\polldetails::get($_args['user_id'], $_args['poll_id']);

		if(is_array(self::$old_answer))
		{
			$old_opt =  array_column(self::$old_answer, 'opt');
		}

		$new_opt = array_keys($_args['answer']);

		if($old_opt == $new_opt && !$_args['skipped'])
		{
			debug::error(T_("You have already selected this answer and submited"), 'answer', 'permission');
			return false;
		}

		$save_offline_chart = self::user_validataion($_args['user_id']);

		self::$must_remove = array_diff($old_opt, $_args['answer']);
		self::$must_insert = array_diff($_args['answer'], $old_opt);

		$old_answer_is_skipped = false;
		$new_answer_is_skipped = false;

		// remove answer must be remove
		foreach (self::$must_remove as $key => $value)
		{
			if($value === '0')
			{
				$old_answer_is_skipped = true;
			}

			$remove_old_answer = \lib\db\polldetails::remove($_args['user_id'], $_args['poll_id'], $value);

			$profile          = 0;
			self::$validation = 'invalid';
			$user_verify      = null;

			foreach (self::$old_answer as $i => $o)
			{
				if($o['opt'] == $value)
				{

					$profile    = $o['profile'];
					self::$validation = $o['validstatus'];
				}

				if(array_key_exists('validstatus', $o))
				{
					$user_verify = $o['validstatus'];
				}
			}

			$answers_details =
			[
				'poll_id'     => $_args['poll_id'],
				'opt_key'     => $value,
				'user_id'     => $_args['user_id'],
				'type'        => 'minus',
				'profile'     => $profile,
				'user_verify' => $user_verify,
				'validation'  => self::$validation,
				'port'        => $_args['port'],
				'subport'     => $_args['subport'],
			];
			// unset user profile if this poll is profile poll
			\lib\utility\profiles::set_profile_by_poll($answers_details);

			\lib\utility\stat_polls::set_poll_result($answers_details);
		}

		$set_option =
		[
			'answer_txt'  => null,
			'validation'  => self::$validation,
			'port'        => $_args['port'],
			'subport'     => $_args['subport'],
			'user_verify' => self::$user_verify,
		];

		if($_args['skipped'] == true)
		{
			$new_answer_is_skipped = true;
			$result  = \lib\db\polldetails::save($_args['user_id'], $_args['poll_id'], 0, $set_option);
			\lib\utility\profiles::people_see_my_poll($_args['poll_id'], "skipped", 'plus');
		}
		elseif(!empty(self::$must_insert))
		{
			foreach ($_args['answer'] as $key => $value)
			{
				$set_option =
				[
					'answer_txt'  => $value,
					'validation'  => self::$validation,
					'port'        => $_args['port'],
					'subport'     => $_args['subport'],
					'user_verify' => self::$user_verify,
				];

				$result = \lib\db\polldetails::save($_args['user_id'], $_args['poll_id'], $key, $set_option);
				// save the poll lucked by profile
				// update users profile
				$answers_details =
				[
					'validation'  => self::$validation,
					'poll_id'     => $_args['poll_id'],
					'opt_key'     => $key,
					'user_id'     => $_args['user_id'],
					'update_mode' => true,
					'user_verify' => self::$user_verify,
					'port'        => $_args['port'],
					'subport'     => $_args['subport'],

				];
				// set user profile if this poll is profile poll
				\lib\utility\profiles::set_profile_by_poll($answers_details);

				if($save_offline_chart)
				{
					\lib\utility\stat_polls::set_poll_result($answers_details);
				}
			}
			\lib\utility\profiles::people_see_my_poll($_args['poll_id'], "answered", 'plus');
		}
		// plus answer update count
		$where =
		[
			'post_id'      => $_args['poll_id'],
			'user_id'      => $_args['user_id'],
			'option_cat'   => "user_detail_$_args[user_id]",
			'option_key'   => "update_answer_$_args[poll_id]",
			'option_value' => "update_answer",
		];
		\lib\db\options::plus($where);
		self::$IS_ANSWERED = [];

		// if($old_answer_is_skipped && $new_answer_is_skipped) || (!$old_answer_is_skipped && !$new_answer_is_skipped)
		// nothing
		// needless to update offline data
		// in dashboard and post rank

		if($old_answer_is_skipped && !$new_answer_is_skipped)
		{
			\lib\db\ranks::minus($_args['poll_id'], 'skip');
			\lib\utility\profiles::minus_dashboard_data($_args['user_id'], "poll_skipped");
			\lib\utility\profiles::people_see_my_poll($_args['poll_id'], "skipped", 'minus');

			\lib\db\ranks::plus($_args['poll_id'], 'vote');
			\lib\utility\profiles::set_dashboard_data($_args['user_id'], "poll_answered");
			\lib\utility\profiles::people_see_my_poll($_args['poll_id'], "answered", 'plus');
		}

		if(!$old_answer_is_skipped && $new_answer_is_skipped)
		{
			\lib\db\ranks::minus($_args['poll_id'], 'vote');
			\lib\utility\profiles::minus_dashboard_data($_args['user_id'], "poll_answered");
			\lib\utility\profiles::people_see_my_poll($_args['poll_id'], "answered", 'minus');

			\lib\db\ranks::plus($_args['poll_id'], 'skip');
			\lib\utility\profiles::set_dashboard_data($_args['user_id'], "poll_skipped");
			\lib\utility\profiles::people_see_my_poll($_args['poll_id'], "skipped", 'plus');
		}

		return debug::true(T_("Your answer updated"));
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function delete($_args)
	{

		$default_args =
		[
			'user_id'    => null,
			'poll_id'    => null,
			'old_answer' => [],
		];

		if(!is_array($_args))
		{
			return false;
		}
		$_args = array_merge($default_args, $_args);

		$old_answer = \lib\db\polldetails::get($_args['user_id'], $_args['poll_id']);

		if(!is_array($old_answer))
		{
			$old_answer = [];
		}

		$old_answer_is_skipped = false;
		foreach ($_args['old_answer'] as $key => $value)
		{
			if(isset($value['key']))
			{
				if($value['key'] === 0)
				{
					$old_answer_is_skipped = true;
				}
			}
			else
			{
				continue;
			}
			self::$validation = 'invalid';
			$profile          = 0;
			$user_verify      = null;

			foreach ($old_answer as $k => $v)
			{
				if(isset($v['opt']) && $v['opt'] == $value['key'])
				{
					if(isset($v['validstatus']))
					{
						self::$validation = $v['validstatus'];
					}
					if(isset($v['profile']))
					{
						$profile = $v['profile'];
					}
				}

				if(array_key_exists('validstatus', $v))
				{
					$user_verify = $v['validstatus'];
				}
			}

			$answers_details =
			[
				'poll_id'     => $_args['poll_id'],
				'opt_key'     => $value['key'],
				'user_id'     => $_args['user_id'],
				'type'        => 'minus',
				'update_mode' => 'delete',
				'profile'     => $profile,
				'validation'  => self::$validation,
				'user_verify' => $user_verify,
			];
			\lib\utility\stat_polls::set_poll_result($answers_details);
		}


		self::$IS_ANSWERED = [];

		$result = \lib\db\polldetails::remove($_args['user_id'], $_args['poll_id']);

		if($result && \lib\db::affected_rows())
		{
			if($old_answer_is_skipped)
			{
				\lib\db\ranks::minus($_args['poll_id'], 'skip');
				\lib\utility\profiles::minus_dashboard_data($_args['user_id'], "poll_skipped");
				\lib\utility\profiles::people_see_my_poll($_args['poll_id'], "skipped", 'plus');
			}
			else
			{
				\lib\db\ranks::minus($_args['poll_id'], 'vote');
				\lib\utility\profiles::minus_dashboard_data($_args['user_id'], "poll_answered");
				\lib\utility\profiles::people_see_my_poll($_args['poll_id'], "answered", 'plus');
			}

			return debug::title(T_("Your answer has been deleted"));
		}
		else
		{
			return debug::error(T_("You have not answered to this poll"));
		}
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