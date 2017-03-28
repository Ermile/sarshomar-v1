<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomar_bot\commands\handle;

class menu
{
	/**
	 * create mainmenu
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function main($_onlyMenu = false)
	{
		// $txt_my = [T_('My polls'), T_('Create')];
		// $user_polls = \lib\db\polls::search(null, ['user_id'=> bot::$user_id, 'get_count' => true, 'my_poll' => true, 'pagenation' => false]);
		// if(!$user_polls)
		// {
		// 	$txt_my = [T_('Create new poll')];
		// }
		// define
		$menu =
		[
			'keyboard' =>
			[
				[T_('Ask me')],
				[T_('Dashboard'), T_('Create new poll')],
			],
			"resize_keyboard" => true
			];

		$user_profile = array_keys(\lib\main::$controller->model()->get_user_profile());
		$original_profile = array_keys(\lib\utility\profiles::profile_data());
		$uncomplate = array_diff($original_profile, $user_profile);

		if(!\lib\utility\sync::is_telegram_sync(bot::$user_id))
		{
			$menu['keyboard'][] = [[
					'text' 				=> T_('Register & sync'),
					'request_contact' 	=> true
				]];
		}
		elseif(!empty($uncomplate))
		{
			$menu['keyboard'][] = [[
					'text' 				=> T_('تکمیل پروفایل')
				]];
		}


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