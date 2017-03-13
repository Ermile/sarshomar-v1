<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\menu;
use \content\saloos_tg\sarshomarbot\commands\utility;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class help
{
	use help\about;
	use help\commands;
	use help\faq;
	use help\feedback;
	use help\privacy;
	public static function start($_query, $_data_url)
	{
		step::stop();
		session::remove_back('expire', 'inline_cache', 'help');
		$method = self::find_method($_query, $_data_url);
		if($method)
		{
			callback_query::edit_message($method);
		}
		return [];
	}

	public static function find_method($_query, $_data_url){
		$method = $_data_url[1];
		$class_name = '\content\saloos_tg\sarshomarbot\commands\callback_query\help';
		if(class_exists($class_name) && method_exists($class_name, $_data_url[1]))
		{
			$call = self::{$_data_url[1]}($_query, $_data_url);
			$call['response_callback'] = utility::response_expire('help');
			$call['parse_mode'] = 'HTML';
			return $call;
		}
		return false;
	}

	public static function home($_query, $_data_url)
	{
		return \content\saloos_tg\sarshomarbot\commands\step_help::start();
	}
}
?>