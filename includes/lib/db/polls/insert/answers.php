<?php
namespace lib\db\polls\insert;
use \lib\debug;
use \lib\db\pollopts;

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
				if(isset($value['title']) && $value['title'] != '')
				{
					$title = trim($value['title']);
					$combine[$key]['title'] = $title;
				}

				$type  = null;
				if(isset($value['type']))
				{
					switch ($value['type'])
					{
						case 'select':
						case 'descriptive':
						// case 'emoji':
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
						$attachment_id = debug::get_msg("result");
					}
				}

				if($attachment_id)
				{
					$combine[$key]['attachment_id'] = $attachment_id;
				}

				// $combine[$key]['desc']          = isset($value['description']) ? trim($value['description']) : null;

				if($type == 'select' || $type == 'emoji' || $type == 'descriptive')
				{
					$object_type = $type;

					if($type == 'descriptive')
					{
						$object_type = 'select';
					}

					// get score value
		     		if(isset($value[$object_type]['score']['value']) && is_numeric($value[$object_type]['score']['value']))
		     		{
		     			$combine[$key]['score'] = $value[$object_type]['score']['value'];
		     		}

		     		// get score group
		 	 		if(isset($value[$object_type]['score']['group']) && is_string($value[$object_type]['score']['group']) && $value[$object_type]['score']['group'])
		     		{
		     			$combine[$key]['groupscore'] = trim($value[$object_type]['score']['group']);
		     		}

		     		// get true answer
		 	 		if(isset($value[$object_type]['is_true']) && $value[$object_type]['is_true'])
		     		{
		     			$combine[$key]['true'] = $value[$object_type]['is_true'];
		     		}
				}

				if(self::$args['permission_profile'] === true)
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

				if(count($combine[$key]) == 1 && isset($combine[$key]['type']))
				{
					unset($combine[$key]);
				}
			}

			if(self::$poll_id)
			{
				$answers = pollopts::set(self::$poll_id, $combine, ['update' => self::$args['update']]);
			}
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
				$support_options["type"]            = (array) ['star','like'];
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