<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;
use \lib\utility\telegram\step;

class step_subscribe
{
	private static $menu = ["hide_keyboard" => true];
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start()
	{
		step::start('subscribe');

		return self::step1();
	}


	/**
	 * show thanks message
	 * @return [type] [description]
	 */
	public static function step1()
	{
		// go to next step
		step::plus();
		if(bot::$user_id)
		{
			// all is users!
		}
		// generate subscribe text
		$final_text = "شما به همه سوالات پاسخ دادید!\n";
		$final_text .= "آیا مایلید مشترک ما شده و پس از اضافه شدن نظرسنجی‌های جدید مطلع شوید؟\n";
		$menu =
		[
			'keyboard' =>
			[
				["بله، علاقمندم مشترک شوم"],
				["خیر، تمایلی ندارم"],
			],
		];
		// get name of question
		$result   =
		[
			[
				'text'         => $final_text,
				'reply_markup' => $menu,
			],
		];
		// return menu
		return $result;
	}


	/**
	 * get user answer for subscribe status
	 * @param  [type] $_feedback [description]
	 * @return [type]            [description]
	 */
	public static function step2($_feedback)
	{
		switch ($_feedback)
		{
			case 'بلع':
			case 'بله،':
			case 'y':
			case 'yes':
				$txt_text = "پس از افزودن شدن نظرسنجی‌های جدید شما به صورت خودکار مطلع خواهید شد:)\n";
				self::saveSubscribe(true);


				break;

			default:
				self::saveSubscribe(false);
				$txt_text = "به منوی اصلی بازگشتیم.\n";
				break;
		}
		step::stop();

		$result   =
		[
			[
				'text'         => $txt_text,
				'reply_markup' => menu::main(true),
			],
		];

		return $result;
	}


	/**
	 * save user subscribe into db
	 * @param  [type] $_status [description]
	 * @return [type]            [description]
	 */
	private static function saveSubscribe($_status = true)
	{
		// set status
		if($_status)
		{
			$_status = 'enable';
		}
		else
		{
			$_status = 'disable';
		}
		$meta       =
		[
			'time'   => date('Y-m-d H:i:s'),
			'status' => $_status,
		];
		$userDetail =
		[
			'cat'    => 'subscribe_'.bot::$user_id,
			'key'    => 'telegram',
			'value'  => 'status',
			'meta'   => $meta,
		];
		// set user_id
		if(isset(bot::$user_id))
		{
			$userDetail['user']   = bot::$user_id;
		}
		$userDetail['status'] = $_status;

		// save in options table
		\lib\utility\option::set($userDetail, true);
	}
}
?>