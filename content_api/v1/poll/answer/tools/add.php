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
		// start transactions
		// \lib\db::transaction();

		$default_optios =
		[
			'method' => 'post',
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_optios, $_options);

		if(!shortURL::is(utility::request('id')))
		{
			return debug::error(T_("Invalid parameter id"), 'id', 'arguments');
		}
		$count_valid_request = 0;

		$answer = utility::request('answer');
		if($answer)
		{
			$count_valid_request++;
		}

		$skip   = utility::request("skip");
		if($skip)
		{
			$count_valid_request++;
		}

		$like   = utility::request("like");
		if($like)
		{
			$count_valid_request++;
		}


		$descriptive   = utility::request("descriptive");
		if($descriptive)
		{
			$count_valid_request++;
		}

		if($count_valid_request > 1)
		{
			return debug::error(T_("You can not set :requests at the same time", ['requests' => implode(',', array_keys(utility::request()))]), 'skip', 'arguments');
		}

		if($count_valid_request === 0)
		{
			return debug::error(T_("You haven't sent any answer"), 'input', 'arguments');
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

			if(!in_array('add', $available['available']) && !in_array('edit', $available['available']) && $answer)
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
			return $this->skip_poll($_options);
		}

		if($answer && !is_array($answer))
		{
			return debug::error(T_("Answer parameter must be array"), 'answer', 'arguments');
		}

		$get_poll_options =
		[
			'check_is_my_poll'   => false,
			'get_filter'         => false,
			'get_opts'           => true,
			'get_options'	     => true,
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
				return debug::error(T_("Poll has not published"), 'id', 'arguments');
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
			return debug::error(T_("This type of poll is not supported to answer"), 'answer', 'arguments');
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

			if(isset($poll['options']['multi']['max']))
			{
				$multi_max = (int) $poll['options']['multi']['max'];
			}
		}

		$ordering = false;
		if(isset($poll['options']['ordering']) && $poll['options']['ordering'])
		{
			$ordering = true;
		}

		if(!$multi && count($answer) > 1)
		{
			return debug::error(T_("This is not a multi select poll and you selected :count answers",['count' => count($answer)]),'answer', 'arguments');
		}

		switch ($poll_type)
		{
			case 'like':
				return $this->like_poll($_options);
				break;

			case 'descriptive':
				return $this->descriptive_poll($_options);
				break;

			default:
			 	// no thing!
			 	break;
		}

		$true_answer = [];

		foreach (utility::request('answer') as $key => $value)
		{
			$poll_answer_key = $key - 1;

			if(!isset($poll['answers'][$poll_answer_key]))
			{
				return debug::error(T_("This poll has not option :key", ['key' => $key]), 'answer', 'arguments');
			}
			else
			{
				if(!isset($poll['answers'][$poll_answer_key]['type']))
				{
					return debug::error(T_("This poll has not answery type of :key", ['key' => $key]), 'answer', 'system');
				}

				$answer_type = $poll['answers'][$poll_answer_key]['type'];

				switch ($answer_type)
				{
					case 'select':
					case 'descriptive':
					case 'emoji':
						// no thing!
						break;
					case 'like':
					case 'upload':
					case 'star':
					case 'notification':
					default:
						if(intval($key) !== 1)
						{
							return debug::error(T_("This poll is :type poll and you can set only one answer", ['type' => $answers_type]), 'answer', 'arguments');
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
									return debug::error(T_("Invalid parameter :value", ['value' => $value]), 'answer', 'arguments');
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

		if($multi)
		{
			$check_multi = true;
			// show best message depending on min and max
			if($multi_min && $multi_max)
			{
				if($multi_min === $multi_max)
				{
					if(count($true_answer) !== $multi_min)
					{
						$check_multi = false;
						$multi_msg = T_("You should exactly select :min options", ["min" => $multi_min]);
					}
				}
				else
				{
					if(count($true_answer) > $multi_max || count($true_answer) < $multi_min)
					{
						$check_multi = false;
						$multi_msg = T_("You can select at least :min and at most :max options", ["min" => $multi_min, "max" => $multi_max ]);
					}
				}
			}
			elseif($multi_min)
			{
				if(count($true_answer) < $multi_min)
				{
					$check_multi = false;
					$multi_msg = T_("You should select at least :min options", ["min" => $multi_min ]);
				}
			}
			elseif($multi_max)
			{
				if(count($true_answer) > $multi_max)
				{
					$check_multi = false;
					$multi_msg = T_("You can select at most :max options", ["max" => $multi_max]);
				}
			}
			else
			{
				// $multi_msg = T_("You can select all of the options");
			}

			if(!$check_multi)
			{
				return debug::error($multi_msg);
			}
		}

		$poll_id = shortURL::decode(utility::request('id'));
		$save =
		[
			'user_id' => $this->user_id,
			'poll_id' => $poll_id,
			'answer'  => $true_answer,
		];

		return $this->save_result($save, $_options);
	}


	/**
	 * skip poll
	 */
	public function skip_poll($_options)
	{
		$save =
		[
			'user_id' => $this->user_id,
			'poll_id' => shortURL::decode(utility::request("id")),
			'skipped'  => true,
		];
		return $this->save_result($save, $_options);
	}


	/**
	 * like a poll
	 *
	 * @param      array   $_options  The options
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function like_poll($_options = [])
	{
		if(utility::request('like'))
		{
			$save =
			[
				'user_id' => $this->user_id,
				'poll_id' => shortURL::decode(utility::request("id")),
				'answer'  => [1 => 'like'],
			];
			return $this->save_result($save, $_options);
		}
		else
		{
			return $this->skip_poll($_options);
		}
	}

	/**
	 * descriptive a poll
	 *
	 * @param      array   $_options  The options
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function descriptive_poll($_options = [])
	{
		if(utility::request('descriptive'))
		{
			$descriptive = utility::request('descriptive');
			$descriptive = trim($descriptive);
			$save =
			[
				'user_id' => $this->user_id,
				'poll_id' => shortURL::decode(utility::request("id")),
				'answer'  => [1 => $descriptive],
			];
			return $this->save_result($save, $_options);
		}
		else
		{
			return $this->skip_poll($_options);
		}
	}


	/**
	 * Saves a result.
	 *
	 * @param      <type>  $_answer   The answer
	 * @param      <type>  $_options  The options
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function save_result($_answer, $_options)
	{
		if($_options['method'] == 'put')
		{
			\lib\utility\answers::update($_answer);
		}
		elseif($_options['method'] == 'post')
		{
			\lib\utility\answers::save($_answer);
		}
		else
		{
			return debug::error(T_("Invalid method"), 'method', 'system');
		}

		if(debug::$status)
		{
			// commit code
			// \lib\db::commit();

			debug::title(T_("Answer saved"));
		}
		else
		{
			// \lib\db::rollback();
		}
	}
}
?>