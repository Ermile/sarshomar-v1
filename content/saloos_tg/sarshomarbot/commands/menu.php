<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;

class menu
{
	/**
	 * create mainmenu
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function main($_onlyMenu = false)
	{
		$txt_my = T_('My polls');
		if(!\lib\db\polls::get(bot::$user_id, 'count'))
		{
			$txt_my = T_('Create new pool');
		}
		// define
		$menu =
		[
			'keyboard' =>
			[
				[T_('Ask from me')],
				[$txt_my],
				[T_('Sarshomar Panel')],
			],
		];

		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = T_('Main menu')."\n\n";

		$result =
		[
			[
				// 'method'       => 'editMessageReplyMarkup',
				'text'         => $txt_text,
				'reply_markup' => $menu,
			],
		];

		// return menu
		return $result;
	}


	/**
	 * create polls menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function polls($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				["نظرسنجی‌های سرشمار"],
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
		$txt_text .= "لطفا از منوی زیر یکی از انواع نظرسنجی‌ها را انتخاب نمایید";
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