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
	 * check last user update
	 * the user can edit the answer for one min and 3 times
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      <type>  $_answer   The answer
	 * @param      array   $_option   The option
	 */
	public static function recently_answered($_user_id, $_poll_id, $_answer, $_option = [])
	{
		$time  = 60; // secend wait for update
		$count = 3;  // num of update poll

		list($must_remove, $must_insert, $old_answer) = self::analyze($_user_id, $_poll_id, $_answer);
		if($must_remove == $must_insert)
		{
			return self::status(false, $_answer, T_("duplicate answer, needless to update"));
		}

		// default insert date
		$insert_time = "2001-01-01 00:00:00";
		foreach ($old_answer as $key => $value)
		{
			$insert_time = $value['insertdate'];
			break;
		}

		$insert_time  = strtotime($insert_time);
		$now          = strtotime("now");
		$diff_seconds = $now - $insert_time;

		if($diff_seconds > $time)
		{
			return self::status(false, $_answer, T_("many time left of your answer, you can not update your answer"));
		}

		// get count of updated the poll
		$where =
		[
			'post_id'    => $_poll_id,
			'user_id'    => $_user_id,
			'option_cat' => "update_user_$_user_id",
			'option_key' => "update_result_$_poll_id"
		];

		\lib\db\options::plus($where);

		$update_count = \lib\db\options::get($where);

		if(!$update_count || !is_array($update_count) || !isset($update_count[0]['value']))
		{
			return self::status(false, $_answer, T_("undefined error was happend!"));
		}

		$update_count = intval($update_count[0]['value']);
		if($update_count > $count)
		{
			return self::status(false, $_answer, T_("a lot update! what are you doing?"));
		}

		return self::status(true, $_answer, T_("you can update your answer"));
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

			$profile = 0;
			foreach ($old_answer as $i => $o)
			{
				if($o['opt'] == $value)
				{
					$profile = $o['profile'];
				}
			}

			$answers_details =
			[
				'poll_id' => $_poll_id,
				'opt_key' => $value,
				'user_id' => $_user_id,
				'type'    => 'minus',
				'profile' => $profile
			];
			\lib\utility\stat_polls::set_poll_result($answers_details);
		}

		foreach ($must_insert as $key => $value)
		{
			self::save($_user_id, $_poll_id, $value, ['in_update' => true]);
			// set the poll stat in save function
		}
		return self::status(true, $_answer, T_("poll answre updated"));
	}
}
?>