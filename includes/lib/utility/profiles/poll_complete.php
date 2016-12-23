<?php
namespace lib\utility\profiles;

trait poll_complete
{

	/**
	 * to save profile value by answered the poll
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function set_profile_by_poll($_args)
	{
		if(
			!isset($_args['poll_id']) ||
			!isset($_args['opt_key']) ||
			!isset($_args['user_id'])
		  )
		{
			return false;
		}

		$profile_lock =
		"
			SELECT
				option_meta AS 'lock'
			FROM
				options
			WHERE
				post_id      = $_args[poll_id] AND
				option_cat   = 'poll_$_args[poll_id]' AND
				option_key   = 'meta' AND
				option_value = 'profile'
			LIMIT 1
			-- profiles::set_profile_by_poll()
		";

		// check this poll has been locked to profile data ?
		$profile_lock = \lib\db::get($profile_lock, 'lock', true);
		if(!$profile_lock)
		{
			return false;
		}

		$answers      = \lib\utility\answers::get($_args['poll_id']);
		$opt_value    = array_column($answers, 'option_value', 'option_key');

		$support_filter = \lib\db\filters::support_filter();
		if(!isset($support_filter[$profile_lock]))
		{
			return false;
		}

		if(preg_match("/^(|opt\_)(\d+)$/", $_args['opt_key'], $user_answer_index))
		{
			$user_answer_index = $user_answer_index[2];
			$user_answer_index--;

			// the user skip the poll
			if($user_answer_index < 0)
			{
				return false;
			}
			if(isset($support_filter[$profile_lock][$user_answer_index]))
			{
				$user_answer = $support_filter[$profile_lock][$user_answer_index];
			}
			else
			{
				return false;
			}
			// get exist profile data of this users
			$profile_data = self::get_profile_data($_args['user_id']);

			// check old profile data by new data get by poll
			if(isset($profile_data[$profile_lock]))
			{
				if($profile_data[$profile_lock] == $user_answer)
				{
					// this user is reliable
					return true;
				}
			}
			return self::set_profile_data($_args['user_id'], [$profile_lock => $user_answer]);
		}
		return false;
	}


	/**
	 * save users change profile in log table
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function save_change_log($_args)
	{
		if(
			!isset($_args['user_id'])    ||
			!isset($_args['key'])		 ||
			!isset($_args['old_value'])  ||
			!isset($_args['new_value'])
		  )
		{
			return false;
		}

		$caller      = "change_$_args[key]";
		$log_item_id = \lib\db\logitems::get_id($caller);
		if(!$log_item_id)
		{
			// list of priority in log item table
			// 'critical','high','medium','low'
			$log_item_priority = null;

			switch ($_args['key'])
			{
				case 'gender':
					$log_item_priority = 'critical';
					break;

				default:
					$log_item_priority = 'high';
					break;
			}

			$insert_log_item =
			[
				'logitem_type'     => 'users',
				'logitem_caller'   => $caller,
				'logitem_title'    => $caller,
				'logitem_desc'     => $caller,
				'logitem_meta'     => null,
				'logitem_priority' => $log_item_priority,
			];
			$log_item_id = \lib\db\logitems::insert($insert_log_item);
			$log_item_id = \lib\db::insert_id();
		}

		if(!$log_item_id)
		{
			return false;
		}

		$insert_log =
		[
			'logitem_id'     => $log_item_id,
			'user_id'        => $_args['user_id'],
			'log_data'       => $_args['key'],
			'log_meta'       => "{\"old\":\"$_args[old_value]\",\"new\":\"$_args[new_value]\"}",
			'log_createdate' => date("Y-m-d H:i:s")
		];
		\lib\db\logs::insert($insert_log);
	}
}
?>