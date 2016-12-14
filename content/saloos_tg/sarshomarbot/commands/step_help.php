<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\utility;

class step_help
{
	private static $menu = ["hide_keyboard" => true];

	public static function start($_text = null)
	{
		return [
			"text"			=> T_("Our support center is at your service."),
			"reply_markup"	=> [
				"inline_keyboard" => [
					[
						['text' => T_('FAQ'), 'callback_data' => 'help/faq'],
						['text' => T_('Commands'), 'callback_data' => 'help/commands'],
					],
					[
						['text' => T_('Feedback'), 'callback_data' => 'help/feedback'],
					],
					[
						['text' => T_('Privacy'), 'callback_data' => 'help/privacy'],
						['text' => T_('About'), 'callback_data' => 'help/about']
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