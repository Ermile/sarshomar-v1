<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomarbot\commands\utility;
use content\saloos_tg\sarshomarbot\commands\make_view;
use \lib\telegram\step;
use \content\saloos_tg\sarshomarbot\commands\menu;

class create_advance
{
	public static function start($_query, $_data_url)
	{
		if(count($_data_url) > 1)
		{
			$method = $_data_url[1];
			$return = self::$method($_query, $_data_url);
		}
		if(is_array($return))
		{
			return $return;
		}
		return [];
	}

	public static function description($_query = null, $_data_url = null)
	{
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		switch (isset($_data_url[2]) ? $_data_url[2] : null) {
			case 'remove':
				step::goingto(2);
				$return = \content\saloos_tg\sarshomarbot\commands\step_create_advance::step2(null, 'remove');
				break;
			default:
				step::goingto(2);
				$return = \content\saloos_tg\sarshomarbot\commands\step_create_advance::step2();
				break;
		}
		callback_query::edit_message($return);
		return [];
	}

	public static function privacy($_query = null, $_data_url = null)
	{
		step::goingto(3);
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		callback_query::edit_message(\content\saloos_tg\sarshomarbot\commands\step_create_advance::step3());
		return [];
	}

	public static function access_profile($_query, $_data_url)
	{
		session::remove_back('expire', 'inline_cache', 'create');
		$poll_request = ['id' => session::get('poll')];
		if($_data_url[2] == 'add')
		{
			$poll_request['access_profile'] = ['displayname'];
		}
		else
		{
			$poll_request['access_profile'] = null;
		}
		utility::make_request($poll_request);
		\lib\main::$controller->model()->poll_add(['method' => 'patch']);
		callback_query::edit_message(\content\saloos_tg\sarshomarbot\commands\step_create_advance::step1());
	}
}
?>