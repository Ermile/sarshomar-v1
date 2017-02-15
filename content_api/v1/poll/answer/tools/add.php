<?php
namespace content_api\v1\poll\answer\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait add
{
	/**
	 * add pollanswer
	 *
	 * @param      array  $_options  The options
	 */
	public function poll_answer_add($_options = [])
	{

		$default_optios =
		[
			'method' => 'post',
		];

		$_options = array_merge($default_optios, $_options);

		if(!shortURL::is(utility::request('id')))
		{
			return debug::error(T_("Invalid parameter id"), 'id', 'arguments');
		}

		$user_answer = utility::request('answer');
		$skip        = utility::request("skip");

		if($user_answer && $skip)
		{
			return debug::error(T_("Can not set answer and skip"), 'skip', 'arguments');
		}

		$available = $this->poll_answer_get($_options);

		debug::title(T_("Can not save answer"));

		if(!debug::$status)
		{
			return;
		}

		if(isset($available['available']) && is_array($available['available']))
		{
			if(!in_array('skip', $available['available']) && $skip)
			{
				return debug::error(T_("Can not skip this poll"), 'answer', 'permission');
			}

			if(!in_array('add', $available['available']) && !in_array('edit', $available['available']) && $user_answer)
			{
				return debug::error(T_("You can not add or edit your answer"), 'answer', 'permission');
			}
		}
		else
		{
			return debug::error(T_("Invalid answer available"), 'api', 'system');
		}

		if($skip)
		{
			return $this->skip_poll();
		}

		if(!is_array($user_answer))
		{
			return debug::error(T_("Answer parameter must be array"), 'answer', 'arguments');
		}

		$get_poll_options =
		[
			'check_is_my_poll'   => false,
			'get_filter'         => false,
			'get_opts'           => true,
			'get_options'	     => false,
			'run_options'	     => false,
			'get_public_result'  => false,
			'get_advance_result' => false,
			'type'               => null, // ask || random
		];

		$poll = $this->poll_get($get_poll_options);

		if(!$poll)
		{
			return debug::error(T_("Poll not found"), 'id', 'arguments');
		}

		if(isset($poll['status']))
		{
			if($poll['status'] != 'publish')
			{
				return debug::error(T_("Poll not publish"), 'id', 'arguments');
			}
		}
		else
		{
			return debug::error(T_("Poll status not found"), 'status', 'system');
		}

		if(!isset($poll['answers']))
		{
			return debug::error(T_("Poll answers not found"), 'id', 'arguments');
		}

		$answers_type = array_column($poll['answers'], 'type');

		$answers_type = array_unique($answers_type);

		$poll_type = null;
		if(count($answers_type) === 1 && isset($answers_type[0]))
		{
			$poll_type = $answers_type[0];
		}
		elseif(count($answers_type) === 2 && in_array('select', $answers_type) && in_array('descriptive', $answers_type))
		{
			$poll_type = $answers_type;
		}

		if(!$poll_type)
		{
			return debug::error(T_("Can not support this poll to answer"), 'answer', 'arguments');
		}

		$multi     = false;
		$multi_min = null;
		$multi_max = null;

		if(isset($poll['options']['multi']))
		{
			$multi = true;

			if(isset($poll['options']['multi']['min']))
			{
				$multi_min = (int) $poll['options']['multi']['min'];
			}

			if(isset($poll['options']['multi']['min']))
			{
				$multi_min = (int) $poll['options']['multi']['min'];
			}
		}

		$ordering = false;
		if(isset($poll['options']['ordering']) && $poll['options']['ordering'])
		{
			$ordering = true;
		}

		if(!$multi && count($user_answer) > 1)
		{
			return debug::error(T_("This poll is not a multi choice poll and you send :count answer",['count' => count($user_answer)]),'answer', 'arguments');
		}

		$true_answer = [];

		foreach (utility::request('answer') as $key => $value)
		{
			$poll_answer_key = $key - 1;

			if(!isset($poll['answers'][$poll_answer_key]))
			{
				return debug::error(T_("This poll have not answer :key", ['key' => $key]), 'answer', 'arguments');
			}
			else
			{
				if(!isset($poll['answers'][$poll_answer_key]['type']))
				{
					return debug::error(T_("This poll have not answer type :key", ['key' => $key]), 'answer', 'system');
				}

				$answer_type = $poll['answers'][$poll_answer_key]['type'];

				switch ($answer_type)
				{
					case 'select':
					case 'descriptive':
						// no thing!
						break;
					case 'upload':
					case 'star':
					case 'like':
					case 'notification':
					default:
						if(intval($key) !== 1)
						{
							return debug::error(T_("This poll is :type poll and you can set answer 1 only", ['type' => $answers_type]), 'answer', 'arguments');
						}
						break;
				}

				if($ordering)
				{
					// ordering mode
				}
				else
				{
					switch ($answer_type)
					{
						case 'select':
							if($value)
							{
								if($value === true)
								{
									$title = null;
									if(isset($poll['answers'][$poll_answer_key]['title']))
									{
										$title = $poll['answers'][$poll_answer_key]['title'];
									}
									$true_answer[$key] = $title;
								}
								elseif($value !== false)
								{
									return debug::error(T_("Invalid paramet :value", ['value' => $value]), 'answer', 'arguments');
								}
							}
							break;

						case 'descriptive':
							$true_answer[$poll_answer_key] = $value;
							break;

						default:
							# code...
							break;
					}
				}
			}
		}

		if(empty($true_answer))
		{
			return debug::error(T_("No answer was set"), 'answer', 'id');
		}

		$poll_id = shortURL::decode(utility::request('id'));
		$save =
		[
			'user_id' => $this->user_id,
			'poll_id' => $poll_id,
			'answer'  => $true_answer,
		];

		if($_options['method'] == 'put')
		{
			\lib\utility\answers::update($save);
		}
		elseif($_options['method'] == 'post')
		{
			\lib\utility\answers::save($save);
		}
		else
		{
			return debug::error(T_("Invalid method"), 'method', 'system');
		}

		if(debug::$status)
		{
			debug::title(T_("Answer saved"));
		}
	}


	/**
	 * skip poll
	 */
	public function skip_poll()
	{
		$save =
		[
			'user_id' => $this->user_id,
			'poll_id' => shortURL::decode(utility::request("id")),
			'skipped'  => true,
		];

		\lib\utility\answers::save($save);

	}
}
?>