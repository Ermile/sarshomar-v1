<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;

class menu_my
{
	/**
	 * create my menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function my($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				["تعریف نظرسنجی جدید"],
				["نظرسنجی‌های موجود"],
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
				'reply_markup' => $menu,
			],
		];

		// return menu
		return $result;
	}


	/**
	 * create my menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function mypolls($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				["تعریف نظرسنجی جدید"],
				["نظرسنجی‌های موجود"],
				[T_('🔙 Back')],
			],
			// "one_time_keyboard" => true,
			// "force_reply"       => true
		];
		if($_onlyMenu)
		{
			return $menu;
		}

		$pollList = \lib\db\polls::get(bot::$user_id, ['id', 'post_title']);
		$txt_list = "شما دارای *". count($pollList). "* نظرسنجی ثبت شده در _name_ هستید.\n";
		$txt_list .= "عناوین نظرسنجی‌های شما به شرح زیر است.\n";
		foreach ($pollList as $key => $value)
		{
			$value = $value;
			$txt_list .= ($key+1). ". [". $value. "](telegram.me/\sarshomarbot?start=poll_$key)\n";
		}

		$txt_text = "*_fullName_*\r\n\n";
		$txt_text .= $txt_list;
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