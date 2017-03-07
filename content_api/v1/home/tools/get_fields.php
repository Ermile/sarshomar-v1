<?php
namespace content_api\v1\home\tools;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

trait get_fields
{
	private static function get_fields(&$_poll_data)
	{

		if(array_key_exists('title', $_poll_data) && $_poll_data['title'] == '‌')
		{
			$_poll_data['title'] = '';
		}

		if(array_key_exists('slug', $_poll_data) && $_poll_data['slug'] == '‌')
		{
			$_poll_data['slug'] = '';
		}

		if(array_key_exists('url', $_poll_data))
		{
			if($_poll_data['url'] == '‌')
			{
				$_poll_data['url'] = '';
			}
			else
			{
				$_poll_data['url'] = rtrim(self::$host.'/'. $_poll_data['url'], '/');
			}
		}

		// encode user id
		if(array_key_exists('user_id', $_poll_data))
		{
			$_poll_data['user_id'] = shortURL::encode($_poll_data['user_id']);
		}

		// encode parent
		if(array_key_exists('parent', $_poll_data))
		{
			$_poll_data['parent'] = shortURL::encode($_poll_data['parent']);
		}

		if(array_key_exists('content', $_poll_data))
		{
			$_poll_data['description'] = $_poll_data['content'];
			unset($_poll_data['content']);
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
		else
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
		else
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

		// change have_true_answer field
		if(array_key_exists('have_true_answer', $_poll_data))
		{
			if($_poll_data['have_true_answer'])
			{
				$_poll_data['have_true_answer'] = true;
			}
			else
			{
				$_poll_data['have_true_answer'] = false;
			}
		}

		// change sarshomar field
		if(array_key_exists('sarshomar', $_poll_data) && $_poll_data['sarshomar'])
		{
			$_poll_data['sarshomar'] = true;
		}
		else
		{
			$_poll_data['sarshomar'] = false;
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

		unset($_poll_data['meta']);
		unset($_poll_data['count']);
		unset($_poll_data['order']);
	}
}
?>