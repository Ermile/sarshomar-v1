<?php
namespace lib\utility\answers;

trait save
{

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
				$result = \lib\db\polldetails::save($_user_id, $_poll_id, $num_of_opt_kye, $set_option);
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

			$result = \lib\db\polldetails::save($_user_id, $_poll_id, $num_of_opt_kye, $_option);
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
}
?>