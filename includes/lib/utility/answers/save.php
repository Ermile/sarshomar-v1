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
				else
				{
					// the user was recently answered to this poll
					$recently_answered = self::recently_answered(...func_get_args());
					if($recently_answered['status'])
					{
						return self::update(...func_get_args());
					}
					// return self::status(false, $is_answered, T_("a lot update! what are you doing?"));
					return self::status(false, $is_answered, $recently_answered['msg']);

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

		if(!is_array($_answer))
		{
			$_answer = [$_answer];
		}

		foreach ($_answer as $key => $value)
		{
			// to save dashoboard data
			if($value == 0)
			{
				$skipped = true;
			}

			$answer_txt = isset($_option['answer_txt'][$value]) ? $_option['answer_txt'][$value] : '';
			$set_option = ['answer_txt' => $answer_txt];
			$set_option = array_merge($_option, $set_option);
			$result = \lib\db\polldetails::save($_user_id, $_poll_id, $value, $set_option);
			// save the poll lucked by profile
			// update users profile
			$answers_details =
			[
				'poll_id' => $_poll_id,
				'opt_key' => $value,
				'user_id' => $_user_id
			];
			// save answered count
			if($value != 'other')
			{
			}
			\lib\utility\stat_polls::set_poll_result($answers_details);
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