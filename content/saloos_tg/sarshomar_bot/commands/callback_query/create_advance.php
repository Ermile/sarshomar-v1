<?php
namespace content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use content\saloos_tg\sarshomar_bot\commands\make_view;
use \lib\telegram\step;
use \content\saloos_tg\sarshomar_bot\commands\menu;

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

	public static function anwers($_query = null, $_data_url = null)
	{
		step::goingto(2);
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		callback_query::edit_message(\content\saloos_tg\sarshomar_bot\commands\step_create_advance::step2());
		return [];
	}
}
?>