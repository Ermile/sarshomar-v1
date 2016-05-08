<?php
namespace content\saloos_tg\sarshomar_bot\commands;
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
			case 'درباره ی':
			case 'درباره‌ی':
				$response = self::about();
				break;

			case '/contact':
			case 'contact':
			case 'تماس':
				$response = self::contact();
				break;

			case '/help':
			case 'help':
			case 'کمک':
				$response = self::help();
				break;

			default:
				break;
		}
		return $response;
	}


	/**
	 * start conversation
	 * @return [type] [description]
	 */
	public static function start()
	{
		$txt_start = "به *_name_* خوش آمدید!\n\n";
		$txt_start .= "کار با _name_ بسیار آسان است.\n";
		$txt_start .= "*نظر دهید*، *نظرسنجی بسازید* و *به اشتراک بگذارید*!\n";
		$txt_start .= "\n\nالبته این داستان ادامه دارد...";
		$result =
		[
			[
				'text'         => $txt_start,
				'reply_markup' => menu::main(true),
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

		$txt_caption = "_name_\n";
		$txt_caption .= "محصولی از ارمایل";

		$txt_text = "_fullName_ محصولی از [ارمایل](http://ermile.ir)\n\n\n";
		$txt_text .= "امکانات _name_\n";
		$txt_text .= "- شرکت در نظرسنجی‌های موجود\n";
		$txt_text .= "- تعریف نظرسنجی جدید\n";
		$txt_text .= "- تعیین محدودیت بر روی مخاطبان نظرسنجی\n";
		$txt_text .= "- به اشتراک گذاری نظرسنجی‌ها\n";
		$txt_text .= "- مشاهده نتایج به تفکیک\n";
		$txt_text .= "- تعریف نظرسنجی جدید\n";


		$result =
		[
			// [
			// 	'caption'   => $txt_caption,
			// 	'method' => 'sendPhoto',
			// 	// 'photo'  => new \CURLFile(realpath("static/images/telegram/sarshomar/about.jpg")),
			// 	'photo'  => 'AgADBAADrKcxG4BMHgvNuFPD7qige8o9QxkABFrJ1mj0gHo4oVIAAgI',
			// ],
			[
				'text'         => $txt_text,
			],
		];


		// $result['method']  = "sendPhoto";
		// full size
		// AgADBAADrKcxG4BMHgvNuFPD7qige8o9QxkABCtWcZS2VF5BoFIAAgI


		return $result;
	}


	/**
	 * show contact message
	 * @return [type] [description]
	 */
	public static function contact()
	{
		// get location address from http://www.gps-coordinates.net/
		$result =
		[
			[
				'method'    => "sendVenue",
				'latitude'  => '34.6349668',
				'longitude' => '50.87914999999998',
				'title'     => 'Ermile | ارمایل',
				'address'   => '#83, Moallem 10, Moallem, Qom, Iran',
			],
		];

		$result[] =
		[
			'text' => "_contact_",
		];

		return $result;
	}


	/**
	 * show help message
	 * @return [type] [description]
	 */
	public static function help()
	{
		$text = "*_fullName_*\r\n\n";
		$text .= "You can control me by sending these commands:\r\n\n";
		$text .= "/start start conversation\n";
		$text .= "/about about\n";
		$text .= "/contact contact us\n";
		$text .= "/menu show main menu\n";
		$text .= "/intro show intro menu\n";
		$text .= "/feature know more about favorite feature\n";
		$text .= "/global read about out global features\n";
		$text .= "/list show list of rooms menu\n";
		$text .= "/standard readmore about standard room\n";
		$text .= "/modern readmore about modern room\n";
		$text .= "/family readmore about family room\n";
		$text .= "/lux readmore about lux room\n";
		// $text .= "/contact contact us\n";
		$result =
		[
			[
				'text'         => $text,
			],
		];

		return $result;
	}

}
?>