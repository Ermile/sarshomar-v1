<?php
namespace content\saloos_tg\sarshomarbot\commands;

use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\utility;

class step_feedback
{
	private static $menu = ["hide_keyboard" => true];
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_text = null)
	{
		step::start('feedback');
		return;
	}


	public static function step1()
	{
		$text = T_("Your valuable opinion uploaded.");
		$text .= "\n";

		$text .= T_("Thank you");
		$text .= "\n";
		$text .= '#'. T_("Feedback");

		step::stop();
		return [
			"text" => $text
		];
	}
}
?>