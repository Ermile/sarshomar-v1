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

			$show_answers = [];
			foreach ($answers as $key => $value)
			{
				$attachment = null;

				if(\content_api\v1\home\tools\api_options::check_api_permission('u','complete_profile', 'admin'))
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
					if(!empty($opt_profile))
					{
						$_poll_data['profile'] = true;
					}
					$answers[$key]['profile'] = $opt_profile;
				}

				// unset($answers[$key]['id']);

				if(isset($value['true']) && $value['true'] == '1')
				{
					$show_answers[$key]['true'] = true;
				}
				else
				{
					$show_answers[$key]['true'] = false;
				}

				if(isset($value['attachment_id']) && $value['attachment_id'])
				{
					$attachment = \lib\db\polls::get_poll($value['attachment_id']);
					$url = null;
					$answers[$key]['file']['id']   = \lib\utility\shortURL::encode($value['attachment_id']);

					if(isset($attachment['meta']) && is_string($attachment['meta']) && substr($attachment['meta'], 0, 1) == '{')
					{
						$attachment['meta'] = json_decode($attachment['meta'], true);
					}

					if(isset($attachment['meta']['url']))
					{
						if(self::$_options['run_options'] && isset($attachment['status']) && $attachment['status'] != 'publish')
						{
							$answers[$key]['file']['url']  = $awaiting_file_url;
						}
						else
						{
							$answers[$key]['file']['url']  = self::$host. '/'. $attachment['meta']['url'];
						}
					}

					if(isset($attachment['meta']['mime']))
					{
						$answers[$key]['file']['mime'] = $attachment['meta']['mime'];
					}
				}

				if(isset($value['groupscore']) && $value['groupscore'])
				{
					$_poll_data['advance_score'] = true;
				}

				unset($answers[$key]['attachmenttype']);
				unset($answers[$key]['attachment_id']);
				unset($answers[$key]['id']);

				$show_answers[$key] = array_filter($answers[$key]);
			}
			// sort($show_naswers);
			$_poll_data['answers'] = $show_answers;
	}
}
?>