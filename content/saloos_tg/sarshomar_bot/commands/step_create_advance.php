<?php
namespace content\saloos_tg\sarshomar_bot\commands;

use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use \content\saloos_tg\sarshomar_bot\commands\markdown_filter;
use \content\saloos_tg\sarshomar_bot\commands\make_view;
use \content\saloos_tg\sarshomar_bot\commands\menu;
use \lib\main;
use \lib\debug;

class step_create_advance
{
	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('create_advance');
		return self::step1();
	}


	public static function step1($_text = null)
	{
		$poll_id = session::get('poll');
		$maker = new make_view($poll_id);
		$maker->messages->add_title();

		$maker->messages->add('title', "\n" . T("شما می‌توانید انواعت تنظیمات دلخواه خود را برای این سوال اعمال کنید")),
		$return = $maker->make();
		$return["response_callback"] = utility::response_expire('create');
		return $return;
	}

	public static function error()
	{
		debug::$status = 1;
		step::stop();
		return [
			'text' => debug::compile()['messages']['error'][0]['title'],
			'reply_markup' => menu::main(true)
		];
	}
}
?>