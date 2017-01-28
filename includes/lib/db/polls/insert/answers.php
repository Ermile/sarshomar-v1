<?php
namespace lib\db\polls\insert;
use \lib\debug;

trait answers
{
	protected static function insert_answers()
	{
		if(self::$args['answers'] && is_array(self::$args['answers']))
		{
			$answers = self::$args['answers'];
			// remove empty index from answer array
			$answers = array_filter($answers);

			// combine answer type and answer text and answer score
			$combine = [];
			foreach ($answers as $key => $value)
			{
				$title = null;
				if(isset($value['title']))
				{
					$title = trim($value['title']);
				}
				$combine[$key]['title'] = $title;

				$type  = null;
				if(isset($value['type']))
				{
					switch ($value['type'])
					{
						case 'select':
						// case 'emoji':
						// case 'descriptive':
						// case 'upload':
						// case 'range':
						// case 'notification':
							$type = $value['type'];
							break;

						default:
							return debug::error(T_("invalid parametr type (:type) in index :key of answer", ['key' => $key, 'type' => $value['type']]),'answer', 'arguments');
							break;
					}
				}
				else
				{
					// return debug::error(T_("invalid parametr answer type in index :key of answer", ['key' => $key]), 'answer', 'arguments');
				}

				if(is_null($type))
				{
					if(isset($value['select']))
					{
						$type = 'select';
					}

					if(isset($value['emoji']))
					{
						$type = 'emoji';
					}

					if(isset($value['descriptive']))
					{
						$type = 'descriptive';
					}

					if(isset($value['upload']))
					{
						$type = 'upload';
					}

					if(isset($value['range']))
					{
						$type = 'range';
					}

					if(isset($value['notification']))
					{
						$type = 'notification';
					}
				}

				$combine[$key]['type'] = $type;

				$attachment_id = null;
				if(isset($value['file']) && $value['file'])
				{
					$upload_answer =
					[
						'upload_name' => $value['file'],
						'file_path'   => $value['file'],
						'user_id'     => self::$args['user']
					];

					$upload_answer = \lib\utility\upload::upload($upload_answer);
					if(\lib\debug::get_msg("result"))
					{
						$attachment_id = \lib\debug::get_msg("result");
					}
				}
				$combine[$key]['attachment_id'] = $attachment_id;

				// $combine[$key]['desc']          = isset($value['description']) ? trim($value['description']) : null;

				if($type == 'select' || $type == 'emoji')
				{
					// get score value
		     		if(isset($value[$type]['score']['value']) && is_numeric($value[$type]['score']['value']))
		     		{
		     			$combine[$key]['score'] = $value[$type]['score']['value'];
		     		}

		     		// get score group
		 	 		if(isset($value[$type]['score']['group']) && is_string($value[$type]['score']['group']))
		     		{
		     			$combine[$key]['groupscore'] = trim($value[$type]['score']['group']);
		     		}

		     		// get true answer
		 	 		if(isset($value[$type]['is_true']) && $value[$type]['is_true'])
		     		{
		     			$combine[$key]['true'] = $value[$type]['is_true'];
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
			}

			$answers_arg =
			[
				'poll_id' => self::$poll_id,
				'answers' => $combine,
				'update'  => self::$args['update'],
			];

			if(self::$poll_id)
			{
				$answers = \lib\utility\answers::insert($answers_arg);
			}
		}
	}
}
?>