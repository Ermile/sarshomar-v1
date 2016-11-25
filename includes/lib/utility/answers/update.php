<?php
namespace lib\utility\answers;

trait update
{

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
			$remove_old_answer = \lib\db\polldetails::remove($_user_id, $_poll_id, $opt_index);

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