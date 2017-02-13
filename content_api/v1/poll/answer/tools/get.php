<?php
namespace content_api\v1\poll\answer\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait get
{
	/**
	 * get pollanswer
	 *
	 * @param      array  $_options  The options
	 */
	public function poll_answer_get($_options = [])
	{


		if(!shortURL::is(utility::request('id')))
		{
			return debug::error(T_("Invalid parameter id"), 'id', 'arguments');
		}

		$poll_id = utility\shortURL::decode(utility::request('id'));

		if($poll_id)
		{
			$current   = [];
			$available = [];
			$is_answer = \lib\utility\answers::is_answered($this->user_id, $poll_id, ['real_answer' => true, 'all_answer' => true]);

			if(!$is_answer)
			{
				$msg = T_("You have not answered this question yet");
				$available = ['add', 'skip'];
			}
			else
			{
				$current = [];
				if(is_array($is_answer))
				{
					foreach ($is_answer as $key => $value)
					{
						if(isset($value['status']) && isset($value['opt']) && $value['status'] == 'enable' && isset($value['txt']))
						{
							$current[$value['opt']] = $value['txt'];
						}
					}
				}

				if(empty($current))
				{
					$msg = T_("You have already answered this question and took your answer");
				}
				else
				{
					$msg = T_("You have answered to option :opt", ['opt' => implode(',', array_keys($current))]);
					foreach ($current as $key => $value)
					{
						if($key == '0')
						{
							$msg = T_("You have already skipped this question");
							$current[$key] = 'skipped';
							break;
						}
					}
				}

				$answer_args =
				[
					'user_id'    => $this->user_id,
					'poll_id'    => $poll_id,
					'port'       => 'site',
					'subport'    => null,
					'debug'      => false,
					'execute'    => false,
				];

				$available = \lib\utility\answers::access_answer($answer_args, 'check');

			}
			debug::title($msg);

			$result = ['my_answer' => $current, 'available' => $available];
			return $result;
		}
		return debug::error(T_("Undefined error"), 'answer', 'system');

	}
}
?>