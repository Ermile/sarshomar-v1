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
		$check = self::check($_user_id, $_poll_id, "poll_status");
		if(!$check->is_ok())
		{
			return $check;
		}

		// check user status to set the chart 'valid' or 'invalid'
		$validation  = 'invalid';
		$user_validstatus = \lib\db\users::get($_user_id, 'user_validstatus');
		if($user_validstatus && ($user_validstatus === 'valid' || $user_validstatus === 'invalid'))
		{
			$validation = $user_validstatus;
		}

		$in_update = false;
		if(isset($_option['in_update']) && $_option['in_update'])
		{
			$in_update = true;
		}
		unset($_option['in_update']);

		if(!is_array($_answer))
		{
			$_answer = [$_answer];
		}
		// if we not in update mod we need to check user answer
		// but in update mod we need to save the user answer whitout check old answer
		// the old answer was check in self::update()
		if(!$in_update)
		{
			// cehck is answer to this poll or no
			$is_answered = self::check($_user_id, $_poll_id, "is_answered");
			if($is_answered->is_ok())
			{
				$time  = 60; // secend wait for update
				$count = 3;  // num of update poll

				if(self::check($_user_id, $_poll_id, "poll_update_result")->is_ok())
				{
					$time  = 60 * 60; // for 1 hours
					$count = 3 * 2;  // for 6 times
				}
				else
				{
					// the user was recently answered to this poll
					$recently_answered = self::check($_user_id, $_poll_id,
						"recently_answered",
						[
							'answers' => $_answer,
							'options' => $_option,
							'time'    => $time,
							'count'   => $count
						]);

					if($recently_answered->is_ok())
					{
						return self::update($_user_id, $_poll_id, $_answer, $_option);
					}
					// return self::status(false, $is_answered, T_("a lot update! what are you doing?"));
					return $recently_answered;
				}
				return self::status(false)
						->set_error_code(3003)
						->set_result($check->get_result())
						->set_opt($_answer);
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

		foreach ($_answer as $key => $value)
		{
			// to save dashoboard data
			if($value == 0)
			{
				$skipped = true;
			}

			$answer_txt = isset($_option['answer_txt'][$value]) ? $_option['answer_txt'][$value] : '';
			$set_option = ['answer_txt' => $answer_txt, 'validation' => $validation];

			$set_option = array_merge($_option, $set_option);
			$result = \lib\db\polldetails::save($_user_id, $_poll_id, $value, $set_option);
			// save the poll lucked by profile
			// update users profile
			$answers_details =
			[
				'validation' => $validation,
				'poll_id'    => $_poll_id,
				'opt_key'    => $value,
				'user_id'    => $_user_id
			];
			// save answered count
			if($value != 'other')
			{
				\lib\utility\stat_polls::set_poll_result($answers_details);
				$update_profile = \lib\utility\profiles::set_profile_by_poll($answers_details);
			}
		}

		if(!$in_update)
		{
			// set dashboard data
			if($skipped)
			{
				/**
				 * plus the ranks
				 * skip mod
				 */
				\lib\db\ranks::plus($_poll_id, 'skip');

				\lib\utility\profiles::set_dashboard_data($_user_id, "poll_skipped");
				\lib\utility\profiles::people_see_my_poll($_user_id, $_poll_id, "skipped");
			}
			else
			{
				/**
				 * plus the ranks
				 * vot mod
				 */
				\lib\db\ranks::plus($_poll_id, 'vot');

				\lib\utility\profiles::set_dashboard_data($_user_id, "poll_answered");
				\lib\utility\profiles::people_see_my_poll($_user_id, $_poll_id, "answered");
			}
		}

		if(\lib\debug::$status)
		{
			return self::status(true)->set_opt($_answer)->set_result($check->get_result())->set_message(T_("Answer Save"));
		}
		else
		{
			return self::status(false)->set_result($check->get_result())->set_message(T_("Error in save your answer"));
		}
	}
}
?>