<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

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
				["بازگشت به منوی اصلی"],
			],
			// "one_time_keyboard" => true,
			"force_reply"       => true
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
				["بازگشت به منوی اصلی"],
			],
			// "one_time_keyboard" => true,
			"force_reply"       => true
		];
		if($_onlyMenu)
		{
			return $menu;
		}

		$pollList = self::getPollList();
		$txt_list = "شما دارای *". count($pollList). "* نظرسنجی ثبت شده در _name_ هستید.\n";
		$txt_list .= "عناوین نظرسنجی‌های شما به شرح زیر است.\n";
		foreach ($pollList as $key => $value)
		{
			$txt_list .= ($key+1). ". [". $value. "](https://sarshomar.com/fa/)\n";
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

	public static function getPollList($_count = false)
	{
		$qry    = "SELECT * FROM posts WHERE post_type = 'poll' AND user_id = ". bot::$user_id;
		// run query
		$result = \lib\db::get($qry, 'post_title');
		if($_count)
		{
			return count($_count);
		}
		// return last insert id
		return $result;
	}
}
?>