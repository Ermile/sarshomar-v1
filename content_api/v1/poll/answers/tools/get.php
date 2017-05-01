<?php
namespace content_api\v1\poll\answers\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait get
{
	public function get_poll_answers_list()
	{
		if(!shortURL::is(utility::request('id')))
		{
			return debug::error(T_("Invalid parameter id"), 'id', 'arguments');
		}

		$poll_id = utility::request('id');
		$from = utility::request("from");
		$to   = utility::request("to");

		if(!preg_match("/^\d+$/", $from))
		{
			if(is_null($from))
			{
				$from = 0;
			}
			else
			{
				return debug::error(T_("Invalid parameter from, from must be integer"), 'from', 'arguments');
			}
		}
		else
		{
			$from = (int) $from;
		}
		if(!preg_match("/^\d+$/", $to))
		{
			if(is_null($to))
			{
				$to = $from + 10;
			}
			else
			{
				return debug::error(T_("Invalid parameter to, to must be integer"), 'to', 'arguments');
			}
		}
		else
		{
			$to = (int) $to;
		}

		if($from > $to)
		{
			return debug::error(T_("Parameter 'from' must be less than parameter 'to'"), 'from', 'arguments');
		}

		$poll = $this->poll_get(['check_is_my_poll' => true]);
		if(debug::$status == 0)
		{
			return;
		}
		$poll_id = shortURL::decode($poll['id']);

		$answrs_list = \lib\db\answerdetails::get_answrs_list($poll_id, [
			'api_mode' => true,
			'from' => $from,
			'length' => ($to - $from),
			]);
		$total = (int) \lib\storage::get_total_record();
		$return = [];
		foreach ($answrs_list as $key => $value) {
			$list = [
				'key'	=> $value['opt'],
				'type'	=> $value['answertype']
			];
			if(isset($poll['access_profile']))
			{
				$list['profile'] = [];
				if(in_array('displayname', $poll['access_profile']))
				{
					$list['profile']['displayname'] = \lib\utility\users::get_user_displayname($value['user_id']);
					$telegram_id = \lib\db\options::get([
						'user_id' => $value['user_id'],
						'option_cat' => 'telegram',
						'option_key' => 'id',
						'limit' => 1
						]);
					if(!empty($telegram_id))
					{
						$list['profile']['telegram_id'] = (int) $telegram_id['value'];
					}
				}
			}

			if($value['answertype'] == 'descriptive')
			{
				$list['text'] = $value['txt'];
			}
			$return[] = $list;

		}
		return [
		'data' => $return,
		'total' => $total,
		'from' => (int) $from,
		'to' => $total < $to ? $total : (int) $to,
		];
	}
}
?>