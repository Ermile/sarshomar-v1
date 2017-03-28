<?php
namespace content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\menu;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class profile
{
	public static function start($_query, $_data_url)
	{
		utility::make_request([$_data_url[1] => $_data_url[2]]);
		\lib\main::$controller->model()->set_user_profile(['method' => 'post']);
		\lib\storage::set_disable_edit(true);
		callback_query::edit_message(\content\saloos_tg\sarshomar_bot\commands\step_profile::start());
	}
}
?>