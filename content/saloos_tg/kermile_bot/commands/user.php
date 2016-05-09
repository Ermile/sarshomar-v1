<?php
namespace content\saloos_tg\kermile_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class user
{
	/**
	 * execute user request and return best result
	 * @param  [type] $_cmd [description]
	 * @return [type]       [description]
	 */
	public static function exec($_cmd)
	{
		$response = null;
		switch ($_cmd['command'])
		{
			case '/start':
			case 'start':
			case 'شروع':
				$response = self::start();
				break;

			case '/about':
			case 'about':
			case 'درباره':
				$response = self::about();
				break;

			default:
				break;
		}

		return $response;
	}


	/**
	 * start
	 * @return [type] [description]
	 */
	public static function start()
	{
		// disable return from main menu
		$txt_text = "سلام، من ربات فست فود ` آزمایشی کرمایل ` هستم.\n چه کاری می خواهید انجام دهید؟";

		$menu =
		[
			'keyboard' =>
			[
				["ثبت سفارش"],
				["درباره ما", "مشاهده منو"],
			],
		];

		$result   =
		[
			[
				'text'         => $txt_text,
				'reply_markup' => $menu,
			],
		];

		return $result;
	}



	/**
	 * show about message
	 * @return [type] [description]
	 */
	public static function about()
	{
		// get location address from http://www.gps-coordinates.net/
		$txt_caption = "_name_ \n". "رستوران بزرگ کرمایل با بهترین خدمات و کادر مجرب در خدمت شماست.";
		$result =
		[
			[
				'method'    => "sendVenue",
				'latitude'  => '34.6349668',
				'longitude' => '50.87914999999998',
				'title'     => 'Ermile | ارمایل',
				'address'   => 'ایران، قم، خیابان معلم۱۰، پلاک۸۳',
				'address'   => '#83, Moallem 10, Moallem, Qom, Iran +9837735183',
			],
			[
				'caption'   => $txt_caption,
				'method' => 'sendPhoto',
				// 'photo'  => new \CURLFile(realpath("static/images/telegram/kermile/about.jpg")),
				'photo'  => 'AgADBAADq6cxGxBxeQwAAVDut79r__Zb5EIZAARi8HlJzJsMYmVdAAIC',
			],
		];

		// $result[] =
		// [
		// 	'text' => "درباره فلان",
		// ];


		// $result['text'] = '['.T_('Sarshomar').'](http://sarshomar.ir)'."\r\n";
		// $result['text'] .= T_("Sarshomar start jumping")."\r\n";
		// $result['text'] .= 'Created and developed by '.ucfirst(core_name);
		return $result;
	}
}
?>