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
				if(substr($_cmd['optional'], 0, 4) === 'poll')
				{
					$opt = substr($_cmd['optional'], 5);
					// show related poll id
				}
				if(substr($_cmd['optional'], 0, 3) === 'ref')
				{
					$opt = substr($_cmd['optional'], 4);
					// save this reference
				}

				$response = self::start();
				break;

			case '/about':
			case 'about':
			case 'درباره':
			case 'درباره ی':
			case 'درباره‌ی':
				$response = self::about();
				break;

			case '/me':
			case 'me':
			case '/whoami':
			case 'whoami':
			case 'من کیم':
			case 'من کیم؟':
			case 'بگیر':
			case 'پروفایل':
			case 'من':
				$response = self::me();
				break;

			case '/contact':
			case 'contact':
			case 'تماس':
			case 'آدرس':
			case 'ادرس':
			case 'نشانی':
				$response = self::contact();
				break;

			case 'type_contact':
				$response = self::register('اطلاعات مخاطب', $_cmd);
				break;

			case 'type_location':
				$response = self::register('آدرس');
				break;

			case 'type_audio':
			case 'type_document':
			case 'type_photo':
			case 'type_sticker':
			case 'type_video':
			case 'type_voice':
			case 'type_venue':
				$response = self::register($_cmd['command'], $_cmd);
				break;

			case '/help':
			case 'help':
			case '؟':
			case '?':
			case 'کمک':
			case 'راهنمایی':
			case '/?':
			case '/؟':
				$response = help::help();
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
	 * get phone number from user contact
	 * @return [type] [description]
	 */
	public static function register($_type = null, $_cmd = null)
	{
		if(!$_type)
		{
			return false;
		}
		// output text
		$text = $_type. ' شما با موفقیت ثبت شد.';
		// if is fake return false;
		switch ($_cmd['command'])
		{
			case 'type_contact':
				if($_cmd['argument'] === 'fake')
				{
					if($_cmd['optional'])
					{
						$text = 'ما به اطلاعات مخاطب شما نیاز داریم، نه سایر کاربران!';
					}
					else
					{
						$text = 'ما برای ثبت‌نام به شماره موبایل احتیاج داریم!';
					}
				}
				break;

			case 'type_audio':
					$text = 'من فرصت آهنگ گوش کردن ندارم!';
				break;

			case 'type_sticker':
					$text = 'ممنون از ابراز لطف شما';
				break;

			case 'type_video':
					$text = 'حسابی سرم شلوفه، فکر نکنم وقت فیلم دیدن باشه!';
				break;

			case 'type_voice':
					$text = 'خیلی مونده تا بخوام صدا رو تشخیص بدم!';
				break;

			default:
					$text = 'من هنوز اونقدر پیشرفته نشدم!';
				break;
		}
		$result =
		[
			[
				'text'  => $text,
			],
		];

		return $result;
	}


	/**
	 * show user details!
	 * @return [type] [description]
	 */
	public static function me()
	{
		$result =
		[
			[
				'method'      => 'getUserProfilePhotos',
			],
		];

		return $result;
	}
}
?>