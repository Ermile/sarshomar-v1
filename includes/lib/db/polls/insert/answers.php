<?php
namespace lib\db\polls\insert;
use \lib\debug;
use \lib\db\pollopts;
use \lib\utility;
use \lib\utility\shortURL;


trait answers
{
	protected static function insert_answers()
	{
		if(!self::isset_args('answers'))
		{
			return ;
		}

		if(!is_array(self::$args['answers']))
		{
			// \lib\db::rollback();
			\lib\db\logs::set('user:poll:add:error:answer:is_not_array', self::$args['user'], ['meta' => ['input' => self::$args]]);
			return debug::error(T_("Answers must be array"), 'answers', 'arguments');
		}

		if(count(self::$args['answers']) > 100)
		{
			// \lib\db::rollback();
			\lib\db\logs::set('user:poll:add:error:answer:max:100', self::$args['user'], ['meta' => ['input' => self::$args]]);
			return debug::error(T_("You can not add more than 100 answers"), 'answers', 'arguments');
		}

		$answers = self::$args['answers'];
		// remove empty index from answer array
		$answers = array_filter($answers);

		// combine answer type and answer text and answer score
		$combine = [];
		foreach ($answers as $key => $value)
		{
			$title = null;
			if(isset($value['title']) && $value['title'] !== '')
			{
				$title = self::safe_user_string($value['title']);
				$combine[$key]['title'] = $title;
			}
			if(mb_strlen($title) > 99)
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:answer:title:100', self::$args['user'], ['meta' => ['input' => self::$args]]);
				debug::error(T_("Invalid answer title argument, you must set less than 99 character for the title in index :key of answer", ['key' => $key + 1]), 'answer', 'arguments');
				return false;
			}

			$type  = null;
			if(isset($value['type']))
			{
				switch ($value['type'])
				{
					case 'select':
					case 'descriptive':
					case 'emoji':
					case 'like':
					// case 'upload':
					// case 'range':
					// case 'notification':
						$type = $value['type'];
						break;

					default:
						// \lib\db::rollback();
						\lib\db\logs::set('user:poll:add:error:answer:type:invalid', self::$args['user'], ['meta' => ['input' => self::$args]]);
						return debug::error(T_("Invalid type (:type) paramater in index :key of answer", ['key' => $key, 'type' => $value['type']]),'answer', 'arguments');
						break;
				}
			}
			else
			{
				// \lib\db::rollback();
				\lib\db\logs::set('user:poll:add:error:answer:type:not_set', self::$args['user'], ['meta' => ['input' => self::$args]]);
				if(self::$debug)
				{
					debug::error(T_("Invalid answer type parameter in index :key of answer", ['key' => $key]), 'answer', 'arguments');
				}
				return ;
			}

			$combine[$key]['type'] = $type;

			$attachment_id = null;
			if(isset($value['file']) && $value['file'])
			{
				$is_attachment = self::is_attachment($value['file']);

				if(!debug::$status)
				{
					return;
				}
				$combine[$key]['attachment_id'] = shortURL::decode($value['file']);

				if(isset($is_attachment['type']))
				{
					$combine[$key]['attachmenttype'] = $is_attachment['type'];
				}
			}

			if($title === null && $attachment_id === null && $type === 'select')
			{
				unset($combine[$key]);
				continue;
			}

			// $combine[$key]['desc']          = isset($value['description']) ? trim($value['description']) : null;

			if($type == 'select' || $type == 'emoji' || $type == 'descriptive' || $type == 'like')
			{
				$object_type = $type;

				if($type == 'descriptive')
				{
					$object_type = 'select';
				}

				// get score value
	     		if(isset($value[$object_type]['score']['value']) && is_numeric($value[$object_type]['score']['value']))
	     		{
	     			if(intval($value[$object_type]['score']['value']) > 9999)
	     			{
						\lib\db\logs::set('user:poll:add:error:answer:score:max', self::$args['user'], ['meta' => ['input' => self::$args]]);
	     				return debug::error(T_("Invalid parameter score in index :key of answer", ['key' => $key]), 'score', 'arguments');
	     			}
	     			$combine[$key]['score'] = $value[$object_type]['score']['value'];
	     		}

	     		// get score group
	 	 		if(isset($value[$object_type]['score']['group']) && is_string($value[$object_type]['score']['group']))
	     		{
	     			if(mb_strlen($value[$object_type]['score']['group']) > 99)
	     			{
						\lib\db\logs::set('user:poll:add:error:answer:score:group:max', self::$args['user'], ['meta' => ['input' => self::$args]]);
	     				return debug::error(T_("You must set less than 99 character group score in index :key of answer", ['key' => $key]), 'group', 'arguments');
	     			}
	     			$combine[$key]['groupscore'] = self::safe_user_string($value[$object_type]['score']['group']);
	     		}

	     		// get true answer
	 	 		if(isset($value[$object_type]['is_true']) && $value[$object_type]['is_true'])
	     		{
	     			if(!is_bool($value[$object_type]['is_true']))
	     			{
	     				return debug::error(T_("Invalid parameter is_true in index :key of answer", ['key' => $key]), 'is_true', 'arguments');
	     			}
	     			$combine[$key]['true'] = 1;
	     		}
			}

			if(self::poll_check_permission('u', 'sarshomar', 'view') === true)
			{
				if(isset($value['profile']) && $value['profile'])
				{
	     			$combine[$key]['profile'] = $value['profile'];
				}
			}

     		// get meta of this object of answer
			$support_answer_object = self::support_answer_object($type);
			$answer_meta           = [];
			foreach ($support_answer_object as $index => $reg)
			{
				$ok = false;
				if(isset($value[$type][$index]))
				{

					if(is_bool($reg) && is_bool($value[$type][$index]))
					{
						$ok = true;
					}
					elseif(is_int($reg) && is_numeric($value[$type][$index]))
					{
						$ok = true;
					}
					elseif(is_string($reg) && is_string($value[$type][$index]))
					{
						$ok = true;
					}
					elseif(is_array($reg))
					{
						$in_array = true;
						if(is_array($value[$type][$index]))
						{
							foreach ($value[$type][$index] as $k => $v)
							{
								if(!in_array($v, $reg))
								{
									$in_array = false;
								}
							}
						}
						elseif(is_string($value[$type][$index]) && in_array($value[$type][$index], $reg))
						{
							$in_array = true;
						}
						else
						{
							$in_array = false;
						}

						$ok = $in_array;
					}
					// check entered parametr and set meta
					if($ok)
					{
						$answer_meta[$index] = $value[$type][$index];
					}
				}
			}

			if(!empty($answer_meta))
			{
				$combine[$key]['meta'] = json_encode($answer_meta, JSON_UNESCAPED_UNICODE);
			}

			if(count($combine[$key]) == 1 && isset($combine[$key]['type']) && $combine[$key]['type'] == 'select')
			{
				unset($combine[$key]);
			}
		}
		// var_dump($combine);exit();
		if(is_array($combine))
		{
			$temp_combine = [];
			foreach ($combine as $key => $value)
			{
				$temp_combine[] = $value;
			}
			$combine = $temp_combine;
		}

		self::$answer_count = count($combine);

		if(self::$poll_id)
		{
			$answers = pollopts::set(self::$poll_id, $combine, ['update' => self::$args['update'], 'method' => self::$args['method']]);
		}

	}


	/**
	 * set the options for every poll type
	 *
	 * @param      <type>  $_poll_type  The poll type
	 */
	private static function support_answer_object($_answer_type)
	{
		$support_options = [];
		switch ($_answer_type)
		{
			case "select" :
				// $support_options["is_true"] 		= true;
				// $support_options["group"]           = (string) 'string';
				// $support_options["value"]           = (int) 1;
				break;

			case "emoji" :
				// $support_options["type"]            = (array) ['star','like'];
				// $support_options["is_true"]         = true;
				// $support_options["group"]           = (string) 'string';
				// $support_options["value"]           = (int) 1;
				$support_options["star_size_min"]   = (int) 1;
				$support_options["star_size_max"]   = (int) 1;
				break;

			case "descriptive" :
				$support_options["text_format"]     = (array) ['any','tel','email','website','number','password','custom'];
				$support_options["text_length_min"] = (int) 1;
				$support_options["text_length_max"] = (int) 1;
				break;

			case "upload" :
				$support_options["file_format"]     = (string) 'string';
				$support_options["file_size_min"]   = (int) 1;
				$support_options["file_size_max"]   = (int) 1;
				break;

			case "range" :
				$support_options["number_size_min"] = (int) 1;
				$support_options["number_size_max"] = (int) 1;

		}
		return $support_options;
	}
}
?>