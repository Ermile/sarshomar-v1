<?php
namespace content_api\v1\home\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait get_answers
{
	public static function get_answers(&$_poll_data)
	{
		$custom_field =
			[
				'id',
				'key',
				'type',
				'title',
				'subtype',
				'true',
				'groupscore',
				'desc',
				'score',
				'attachment_id',
				'attachmenttype',
			];

			$answers = \lib\db\pollopts::get(self::$private_poll_id, $custom_field, true);
			// var_dump($answers);
			$show_answers = [];
			foreach ($answers as $key => $array)
			{
				if(!is_array($array))
				{
					continue;
				}

				$attachment = null;
				foreach ($array as $field => $value)
				{
					switch ($field)
					{
						case 'id':
						case 'attachmenttype':
						case 'desc':
							// no thing!
							break;

						case 'attachment_id':
							if($value)
							{
								$attachment = \lib\db\polls::get_poll($value);
								$url = null;
								$show_answers[$key]['file']['id']   = \lib\utility\shortURL::encode($value);

								if(isset($attachment['meta']) && is_string($attachment['meta']) && substr($attachment['meta'], 0, 1) == '{')
								{
									$attachment['meta'] = json_decode($attachment['meta'], true);
								}

								$attachment_type = null;

								if(isset($attachment['meta']['mime']))
								{
									$attachment_type = $attachment['meta']['mime'];
								}

								$show_answers[$key]['file']['mime'] = $attachment_type;

								if(isset($attachment['meta']['url']))
								{
									if(self::$_options['run_options'] && isset($attachment['status']) && $attachment['status'] != 'publish')
									{
										$show_answers[$key]['file']['url']  = self::awaiting_file_url($attachment_type);
									}
									else
									{
										$show_answers[$key]['file']['url']  = self::host('file'). '/'. $attachment['meta']['url'];
									}
								}

							}
							break;

						case 'key':
							$show_answers[$key][$field] = (int) $value;
							break;

						case 'score':
							if(isset($value))
							{
								$show_answers[$key][$field] = (int) $value;
								$_poll_data['have_score'] = true;
							}
							else
							{
								$show_answers[$key][$field] = null;
							}

							break;

						case 'type':
						case 'title':
						case 'subtype':
						case 'groupscore':
							if(isset($value))
							{
								$show_answers[$key][$field] = (string) $value;
							}
							else
							{
								$show_answers[$key][$field] = null;
							}

							if($field === 'groupscore' && isset($value))
							{
								$_poll_data['advance_score'] = true;
							}
							break;

						case 'true':
							if(isset($value['true']) && $value['true'] == '1')
							{
								$show_answers[$key]['true'] = true;
							}
							else
							{
								$show_answers[$key]['true'] = false;
							}
							break;

						default:
							# code...
							break;
					}

				}

				if(\content_api\v1\home\tools\api_options::check_api_permission('u','complete_profile', 'admin'))
				{
					$show_answers[$key]['profile'] = self::load_profile_lock($array);
					if(!empty($show_answers[$key]['profile']))
					{
						$_poll_data['profile'] = true;
					}
				}
			}

			$_poll_data['answers'] = $show_answers;
	}


	private static function load_profile_lock(&$value)
	{
		$opt_profile = [];
		if(isset($value['id']))
		{
			$profile = \lib\db\terms::usage($value['id'], [], 'profile', 'sarshomar');

			if($profile && is_array($profile))
			{
				foreach ($profile as $k => $v)
				{
					if(isset($v['id']))
					{
						$opt_profile[$k]['id'] = shortURL::encode($v['id']);
					}

					if(isset($v['term_title']))
					{
						if($v['term_title'] != T_($v['term_title']))
						{
							$opt_profile[$k]['title'] = $v['term_title'] . " | ". T_($v['term_title']);
						}
						else
						{
							$opt_profile[$k]['title'] = $v['term_title'];
						}
					}
					if(isset($v['term_meta']) && is_string($v['term_meta']) && substr($v['term_meta'], 0,1) === '{')
					{
						$temp_term_meta = json_decode($v['term_meta'], true);

						if(isset($temp_term_meta['translate'][self::$current_language]))
						{
							$opt_profile[$k]['translate'] = $temp_term_meta['translate'][self::$current_language];
						}
					}
				}
			}
		}

		return $opt_profile;
	}
}
?>