<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;

class step_help
{
	private static $menu = ["hide_keyboard" => true];

	public static function start($_text = null)
	{
		return [
			"text"			=> "please selecet your menue",
			"reply_markup"	=> [
				"inline_keyboard" => [
					[
						['text' => 'faq', 'callback_data' => 'help/faq'],
						['text' => 'commands', 'callback_data' => 'help/commands'],
					],
					[
						['text' => 'feedback', 'callback_data' => 'help/feedback'],
					],
					[
						['text' => 'privacy', 'callback_data' => 'help/privacy'],
						['text' => 'about', 'callback_data' => 'help/about']
					]
				]
			],
			"response_callback" => utility::response_expire('help')
		];
	}

	public static function exec($_command)
	{
		$command = substr($_command, 1, strlen($_command));
		return callback_query\help::find_method([], ['help', $command]);
	}
}
?>