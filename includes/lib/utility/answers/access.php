<?php
namespace lib\utility\answers;

trait access
{
	/**
	 * check the access answer the user on this poll
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 * @param      <type>  $_answer   The answer
	 * @param      string  $_type     The type
	 */
	public static function check($_user_id, $_poll_id, $_type = "all", $_option = [])
	{
		$time    = isset($_option['time']) 		? $_option['time'] 		: 60;
		$count   = isset($_option['count']) 	? $_option['count'] 	: 3 ;
		$answers  = isset($_option['answers']) 	? $_option['answers']	: [];
		$options = isset($_option['options']) 	? $_option['options'] 	: [];

		switch ($_type)
		{
			case 'all':
				$poll_status =  self::check_status($_poll_id);
				if(!$poll_status->is_ok())
				{
					return $poll_status;
				}

				$recently_answered =
				self::recently_answered($_user_id, $_poll_id, $answers, $options, $time, $count);
				if(!$recently_answered->is_ok())
				{
					return $recently_answered;
				}

				return self::status(true)->set_result($recently_answered->get_result());

				break;
			case 'poll_update_result':
				$poll_update = \lib\db\polls::check_meta($_poll_id, "update_result");
				if($poll_update)
				{
					return self::status(true);
				}
				return self::status(false);

				break;

			case 'recently_answered':
				return self::recently_answered($_user_id, $_poll_id, $answers, $options, $time, $count);
				break;
			case 'is_answered':
				$is_answered = self::is_answered($_user_id, $_poll_id);
				if($is_answered)
				{
					return self::status(true)->set_result($is_answered);
				}
				return self::status(false)->set_result($is_answered);

				break;
			case 'poll_status':
				return self::check_status($_poll_id);
				break;
		}
		// return self::status(true);
	}


	/**
	 * check poll status
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function check_status($_poll_id)
	{
		// check poll status
		$poll = \lib\db\polls::get_poll($_poll_id);
		if(!isset($poll['status']))
		{
			// poll not found
			return self::status(false)->set_error_code(3000)->set_result($poll);
		}
		elseif($poll['status'] == 'deleted')
		{
			// poll is deleted
			return self::status(false)->set_error_code(3001)->set_result($poll);
		}
		elseif($poll['status'] != 'publish')
		{
			// poll not published
			return self::status(false)->set_error_code(3002)->set_result($poll);
		}
		return self::status(true);
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
	public static function recently_answered($_user_id, $_poll_id, $_answer = [], $_option = [], $_time = 60, $_count = 3)
	{
		$time  = $_time; // secend wait for update
		$count = $_count;  // num of update poll

		list($must_remove, $must_insert, $old_answer) = self::analyze($_user_id, $_poll_id, $_answer);
		if($_answer !== [])
		{
			if($must_remove == $must_insert)
			{
				return self::status(false)->set_opt($_answer)->set_result($old_answer)->set_error_code(3004);
			}
		}

		// default insert date
		$insert_time = date("Y-m-d H:i:s");
		$now         = time();
		foreach ($old_answer as $key => $value)
		{
			$insert_time = $value['insertdate'];
			break;
		}

		$insert_time  = strtotime($insert_time);
		$diff_seconds = $now - $insert_time;

		if($diff_seconds > $time)
		{
			return self::status(false)->set_opt($_answer)->set_result($old_answer)->set_error_code(3005);
		}

		// get count of updated the poll
		$where =
		[
			'post_id'    => $_poll_id,
			'user_id'    => $_user_id,
			'option_cat' => "update_user_$_user_id",
			'option_key' => "update_result_$_poll_id",
		];
		if($_answer !== [])
		{
			\lib\db\options::plus($where);
		}

		$where['limit'] = 1;

		$update_count = \lib\db\options::get($where);

		if(!$update_count || !is_array($update_count) || !isset($update_count['value']))
		{
			return self::status(true)->set_message(T_("No problem"));
		}

		$update_count = intval($update_count['value']);
		if($update_count > $count)
		{
			return self::status(false)->set_opt($_answer)->set_result($old_answer)->set_error_code(3006);
		}

		return self::status(true)->set_opt($_answer)->set_result($old_answer)->set_message(T_("You can update your answer"));
	}
}
?>