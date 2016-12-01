<?php
namespace lib\utility\answers;

trait update
{

	/**
	 * analyze the old answer and new answer to make list of must_insert and must_remove
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function analyze($_user_id, $_poll_id, $_answer)
	{
			// get the old answered user to this poll
		$old_answer = \lib\db\polldetails::get($_user_id, $_poll_id);

		// error in poll detail record
		if(!$old_answer || !is_array($old_answer))
		{
			// to we have not bug of foreach
			return [[], [], []];
		}

		if(!is_array($_answer))
		{
			$_answer = [$_answer];
		}

		// make a array similar the answer array
		$opt_list =  array_column($old_answer, 'opt');
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
		$must_remove = array_diff($opt_list, $_answer);
		$must_insert = array_diff($_answer, $opt_list);

		return [$must_remove, $must_insert, $old_answer];
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
		list($must_remove, $must_insert, $old_answer) = self::analyze($_user_id, $_poll_id, $_answer);

		// remove answer must be remove
		foreach ($must_remove as $key => $value)
		{
			$remove_old_answer = \lib\db\polldetails::remove($_user_id, $_poll_id, $value);

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
				'poll_id'    => $_poll_id,
				'opt_key'    => $value,
				'user_id'    => $_user_id,
				'type'       => 'minus',
				'profile'    => $profile,
				'validation' => $validation
			];
			\lib\utility\stat_polls::set_poll_result($answers_details);
		}

		foreach ($must_insert as $key => $value)
		{
			$_option['in_update'] = true;
			self::save($_user_id, $_poll_id, $value, $_option);
			// set the poll stat in save function
		}
		return self::status(true)->set_opt($_answer)->set_message(T_("Your answer updated"));
	}


	/**
	 * change the user validation
	 * the user was in 'awaiting' status and
	 * we save all answers of this user in 'invalid' type of poll stats
	 * now the user active her account
	 * we change all stats the user was answered to it to 'valid' status
	 *
	 * @param      <type>  $_user_id  The user identifier
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
						'validation' => 'valid',
						'poll_id'    => $value['post_id'],
						'opt_key'    => $value['opt'],
						'user_id'    => $_user_id
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