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

		$this->poll_get();

		if(!debug::$status)
		{
			return;
		}

		$poll_id = utility\shortURL::decode(utility::request('id'));

		if($poll_id)
		{
			$current   = [];
			$available = [];

			$is_answer = \lib\utility\answers::is_answered($this->user_id, $poll_id);
			if(!isset($is_answer[0]))
			{
				$is_answer = [$is_answer];
			}

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
						if(
							isset($value['opt']) 					&&
							array_key_exists('txt', $value) 		&&
							array_key_exists('answertype', $value) 	&&
							array_key_exists('status', $value) 		&&
							$value['status'] == 'enable'
						  )
						{
							if($value['opt'] === '0')
							{
								$current[] = ['key' => (int) $value['opt'], 'type' => "skipped"];
							}
							else
							{
								$current[] = ['key' => (int) $value['opt'], 'type' => $value['answertype'], $value['answertype'] => $value['txt']];
							}
						}
					}
				}

				if(empty($current))
				{
					$msg = T_("You have already answered this question and took your answer");
				}
				else
				{
					if(!empty($current) && is_array($current))
					{
						$msg = T_("You have answered to option :opt", ['opt' => implode(',', array_column($current, 'key'))]);
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
		else
		{
			return debug::error(T_("Can not found poll id"), 'api', 'system');
		}

	}
}
?>