<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;

class menu_civility
{
	/**
	 * create civility menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function civility($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				[""],
				["مردمی", "روانشناسی"],
				[T_('🔙 Back')],
			],
			// "one_time_keyboard" => true,
			// "force_reply"       => true
		];
		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = "*_fullName_*\r\n\n";
		$txt_text .= "بخش نظرسنجی‌های مردمی به زودی راه‌اندازی خواهد شد";
		$result   =
		[
			[
				'text'         => $txt_text,
				// 'reply_markup' => $menu,
			],
		];

		// return menu
		return $result;
	}
}
?>