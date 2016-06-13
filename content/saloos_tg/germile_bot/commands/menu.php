<?php
namespace content\saloos_tg\germile_bot\commands;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class menu
{
	public static $return = false;

	public static function exec($_cmd)
	{
		$response = null;
		switch ($_cmd['command'])
		{
			case 'main':
			case '/main':
			case 'mainmenu':
			case 'menu':
			case '/menu':
			case 'منو۰':
				$response = self::main();
				break;

			case 'return':
			case 'بازگشت':
				switch ($_cmd['text'])
				{
					case 'بازگشت به منوی اصلی':
					default:
						$response = user::start();
						break;
					case 'بازگشت به ثبت سفارش':
						$response = self::order();
						break;
				}
				// $response = self::returnBtn();
				break;

			default:
				break;
		}

		// automatically add return to end of keyboard
		if(self::$return)
		{
			// if has keyboard
			if(isset($response['reply_markup']['keyboard']))
			{
				$response['reply_markup']['keyboard'][] = ['بازگشت'];
			}
		}

		return $response;
	}


	/**
	 * showMenu
	 * @return [type] [description]
	 */
	public static function showMenu()
	{
		// $menu =
		// [
		// 	'keyboard' =>
		// 	[
		// 		["ساندویچ", "پیتزا"],
		// 		["مخلفات", "نوشیدنی"],
		// 	],
		// ];

		$txt_caption = "محصولات فست فود کرمایل.\nشما می توانید منوی ما را در گوشی یا رایانه خود ذخیره کنید.";
		$result =
		[
			[
				'caption'   => $txt_caption,
				'method' => 'sendPhoto',
				// 'photo'  => new \CURLFile(realpath("static/images/telegram/germile/menu.jpg")),
				// 'photo'  => 'AgADBAADracxG0lI6AwPh7ImUJln84h_aTAABMkTUZCw5Z5YNz4CAAEC', // 1920
				'photo'  => 'AgADBAADracxG0lI6AwPh7ImUJln84h_aTAABDp6iOmMIhHhOD4CAAEC', // 1280
				// 'photo'  => 'AgADBAADracxG0lI6AwPh7ImUJln84h_aTAABCUgYxIXz9KBOz4CAAEC', // 800
			],
		];
		// $result['reply_markup'] = $menu;

		// $result   =
		// [
		// 	[
		// 		'text'         => "لطفا یکی از دسته بندی ها را انتخاب کنید",
		// 		'reply_markup' => $menu,
		// 	],
		// ];


		return $result;
	}


	/**
	 * create mainmenu
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function main($_onlyMenu = false)
	{
		// define
		$menu =
		[
			'keyboard' =>
			[
				["ثبت سفارش"],
				["مشاهده منو"],
				["درباره", "ثبت بازخورد"],
			],
		];

		if($_onlyMenu)
		{
			return $menu;
		}

		$txt_text = "منوی اصلی\n\n";

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
	 * return menu
	 * @return [type] [description]
	 */
	public static function main_old()
	{
		// disable return from main menu
		self::$return          = false;
		$result['text']        = 'منوی اصلی'."\r\n";
		$result['reply_markup'] =
		[
			'keyboard' =>
			[
				["ثبت سفارش"],
				["مشاهده منو"],
				["درباره", "ثبت بازخورد"],
			],
		];
		return $result;
	}
}
?>