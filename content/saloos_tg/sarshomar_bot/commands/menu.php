<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class menu extends \content\saloos_tg\sarshomar_bot\controller
{
	public static function exec($_cmd)
	{
		$response = null;
		switch ($_cmd['command'])
		{
			case 'menu':
			case 'منو':
				$response = self::menu0();
				break;

			case 'loc':
			case 'موقعیت':
				$response = self::menu_loc();
				break;


			case 'inline':
			case 'اینلاین':
				$response = self::menu_inline();
				break;

			case 'mainmenu':
			case 'menu0':
			case 'منو۰':
				$response = self::menu_main();
				break;

			case 'return':
			case 'بازگشت':
				$response = self::return();
				break;

			default:
				break;
		}

		return $response;
	}


	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function return()
	{
		$result['text'] = 'بازگشت به منوی قبلی'."\r\n";
		$result['text'] .= 'مثلا برگشتیم'."\r\n";
		$result['replyMarkup'] =
		[
			'keyboard' =>
			[
					["خوب"],
					["بد"],
			],
		];
		return $result;
	}


	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function menu0()
	{
		$result['text'] = 'منوی آزمایشی'."\r\n";
		$result['replyMarkup'] =
		[
			'keyboard' =>
			[
					["سلام"],
					["خوبی"],
					["خوب", "بد"],
			],
		];
		return $result;
	}


	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function menu_main()
	{
		$result['text'] = 'منوی اصلی'."\r\n";
		$result['replyMarkup'] =
		[
			'keyboard' =>
			[
					["منوی آزمایش"],
					["نظرسنجی های من"],
					["مقالات"],
					["پروفایل"],
			],
		];
		return $result;
	}


	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function menu_loc()
	{
		$result['text'] = 'منوی موقعیت'."\r\n";
		$result['replyMarkup'] =
		[
			'keyboard' =>
			[
				[
					[
						'text' => 'تقاضای شماره تلفن',
						'request_contact' => true
					],
					[
						'text' => 'تقاضای آدرس',
						'request_location' => true
					]
				]
			]
		];
		return $result;
	}


	/**
	 * return menu
	 * @return [type] [description]
	 */
	public static function menu_inline()
	{
		$result['text'] = 'منوی اینلاین آزمایشی'."\r\n";
		$result['replyMarkup'] =
		[
			'inline_keyboard' =>
			[
				[
					[
						'text' => '<',
						'callback_data' => 'go_left'
					],
					[
						'text' => '^',
						'callback_data' => 'go_up'
					],
					[
						'text' => '>',
						'callback_data' => 'go_right'
					],
				],
				[
					[
						'text' => 'open google.com',
						'url' => 'google.com'
					],
					[
						'text' => 'search \'test\' inline',
						'switch_inline_query' => 'test'
					],
				]
			],
		];
		return $result;
	}
}
?>