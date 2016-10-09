<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;

class menu_language
{
	/**
	 * create my menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function set_one($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				["فارسی 🇮🇷"],
				["English"],
			],
			// "one_time_keyboard" => true,
			// "force_reply"       => true
		];
		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = "*_fullName_*\r\n\n";
		$result   =
		[
			[
				'text'         => $txt_text,
				'reply_markup' => $menu,
			],
		];

		// return menu
		return $result;
	}
}
?>