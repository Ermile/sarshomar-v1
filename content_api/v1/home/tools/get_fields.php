<?php
namespace content_api\v1\home\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait get_fields
{
	private static function get_fields(&$_poll_data)
	{

		if(array_key_exists('url', $_poll_data))
		{
			if($_poll_data['url'] === '‌')
			{
				$_poll_data['url'] = null;
			}
			else
			{
				$_poll_data['url'] = self::host('with_language'). '/'. $_poll_data['url'];
			}
		}

		// encode user id
		if(array_key_exists('user_id', $_poll_data))
		{
			$_poll_data['user_id'] = (string) shortURL::encode($_poll_data['user_id']);
		}

		// encode parent
		if(array_key_exists('parent', $_poll_data))
		{
			$_poll_data['parent'] = (string) shortURL::encode($_poll_data['parent']);
		}

		if(array_key_exists('content', $_poll_data))
		{
			$_poll_data['description'] = $_poll_data['content'];
		}

		if(array_key_exists('date', $_poll_data) && $_poll_data['date'])
		{
			if(\lib\define::get_language() === 'fa')
			{
				$_poll_data['date'] = \lib\utility\jdate::date("Y-m-d", $_poll_data['date']);
			}
			else
			{
				$_poll_data['date'] = date("Y-m-d", strtotime($_poll_data['date']));
			}
		}


		// encode suervey
		if(array_key_exists('survey', $_poll_data))
		{
			$_poll_data['survey'] = shortURL::encode($_poll_data['survey']);
		}

		// change have_score field
		if(array_key_exists('have_score', $_poll_data))
		{
			if($_poll_data['have_score'])
			{
				$_poll_data['have_score'] = true;
			}
			else
			{
				$_poll_data['have_score'] = false;
			}
		}

		// change is_answered field
		if(array_key_exists('is_answered', $_poll_data))
		{
			if($_poll_data['is_answered'])
			{
				$_poll_data['is_answered'] = true;
			}
			else
			{
				$_poll_data['is_answered'] = false;
			}
		}
		else
		{
			$is_answered = utility\answers::is_answered(self::$private_user_id, self::$private_poll_id);

			if($is_answered)
			{
				$_poll_data['is_answered'] = true;
			}
			else
			{
				$_poll_data['is_answered'] = false;
			}
		}

		// change my_like field
		if(array_key_exists('my_like', $_poll_data))
		{
			if($_poll_data['my_like'])
			{
				$_poll_data['my_like'] = true;
			}
			else
			{
				$_poll_data['my_like'] = false;
			}
		}
		elseif(self::$_options['run_options'])
		{
			$my_like =
			[
				'post_id'       => self::$private_poll_id,
				'option_key'    => 'like',
				'option_status' => 'enable',
				'option_cat'    => 'user_detail_'. self::$private_user_id,
				'user_id'       => self::$private_user_id,
				'limit'         => 1,
			];

			$my_like = \lib\db\options::get($my_like);
			if($my_like)
			{
				$_poll_data['my_like'] = true;
			}

		}

		// change my_fav field
		if(array_key_exists('my_fav', $_poll_data))
		{
			if($_poll_data['my_fav'])
			{
				$_poll_data['my_fav'] = true;
			}
			else
			{
				$_poll_data['my_fav'] = false;
			}
		}
		elseif(self::$_options['run_options'])
		{
			$my_fav =
			[
				'post_id'       => self::$private_poll_id,
				'option_key'    => 'fav',
				'option_status' => 'enable',
				'option_cat'    => 'user_detail_'. self::$private_user_id,
				'user_id'       => self::$private_user_id,
				'limit'         => 1,
			];

			$my_fav = \lib\db\options::get($my_fav);

			if($my_fav)
			{
				$_poll_data['my_fav'] = true;
			}

		}

		if(isset($_poll_data['meta']['summary']))
		{
			$_poll_data['summary'] = $_poll_data['meta']['summary'];
			unset($_poll_data['meta']['summary']);
		}

		if(isset($_poll_data['meta']['access_profile']))
		{
			$_poll_data['access_profile'] = $_poll_data['meta']['access_profile'];
			unset($_poll_data['meta']['access_profile']);
		}

		foreach ($_poll_data as $key => $value)
		{
			switch ($key)
			{
				case 'id':
				case 'comment':
				case 'language':
				case 'privacy':
				case 'short_url':
				case 'status':
				case 'type':
				case 'user_id':
				case 'title':
				case 'url':
					$_poll_data[$key] = (string) $value;
					break;

					// $_poll_data[$key] = self::host(). $value;
					// break;

				case 'count_comment':
				case 'count_fav':
				case 'count_like':
				case 'count_vote':
					$_poll_data[$key] = (int) $value;
					break;

				case 'description':
				case 'parent':
				case 'slug':
				case 'summary':
				case 'survey':
					if(isset($value) && !is_null($value) && $value !== '‌' && $value != '')
					{
						$_poll_data[$key] = (string) $value;
					}
					else
					{
						$_poll_data[$key] = null;
					}
					break;

				case 'have_score':
				case 'have_true_answer':
				case 'is_answered':
				case 'profile':
				case 'sarshomar':
					if($value)
					{
						$_poll_data[$key] = true;
					}
					else
					{
						$_poll_data[$key] = false;
					}
					break;

				case 'answers':
				case 'articles':
				case 'filters':
				case 'options':
				case 'result':
				case 'tags':
				default:
					continue;
					break;
			}
		}
		unset($_poll_data['meta']);
		unset($_poll_data['count']);
		unset($_poll_data['order']);
		unset($_poll_data['content']);
	}
}
?>