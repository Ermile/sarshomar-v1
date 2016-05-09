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
			case 'آدرس':
			case 'ادرس':
			case 'نشانی':
				$response = self::contact();
				break;

			case '/help':
			case 'help':
			case '؟':
			case '?':
			case 'کمک':
			case 'راهنمایی':
			case '/?':
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

		$txt_text = "_name_ برای انجام آسان و سریع نظرسنجی‌های دقیق و به‌دور از شبهه در مقیاس وسیع و با هزینه مناسب طراحی شده است.\n";
		$txt_text .= "انجام عمل نظرسنجی که ما آن را _name_ نامیده‌ایم اولین و آخرین طرح برای این کار نبوده و نخواهد بود ولی ما تلاش داریم تا امکاناتی که تاکنون وجود نداشته و یا سخت قابل دستیابی بوده را به راحتی در اختیار شما قرار دهیم.\n\n";
		$txt_text .= "امیدواریم در این راه طولانی بتوانیم انتظارات شما را برآورده نماییم.\n";

		$txt_text .= "\n\n\n_fullName_ محصولی از [ارمایل](https://ermile.com/fa/)\n\n\n";
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
				'address'   => 'ایران، قم، خیابان معلم۱۰، پلاک۸۳',
				'address'   => '#83, Moallem 10, Moallem, Qom, Iran +9837735183',
			],
		];

		// $result[] =
		// [
		// 	'text' => "_contact_",
		// ];

		return $result;
	}


	/**
	 * show help message
	 * @return [type] [description]
	 */
	public static function help()
	{
		$text = "*_fullName_*\r\n\n";
		$text .= "دستورات زیر برای کار با ربات سرشمار در دسترس شماست:\r\n\n";
		// $text .= "/menu show main menu\n";
		// $text .= "/intro show intro menu\n";
		$text .= "/contact تماس با ما\n";
		$text .= "/about درباره _name_\n";
		$text .= "/start شروع مجدد\n";
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