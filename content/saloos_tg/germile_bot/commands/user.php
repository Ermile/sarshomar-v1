<?php
namespace content\saloos_tg\germile_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;

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

			case '/contact':
			case 'contact':
			case 'تماس':
			case 'آدرس':
			case 'ادرس':
			case 'نشانی':
				$response = self::contact();
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
	 * start
	 * @return [type] [description]
	 */
	public static function start()
	{
		// disable return from main menu
		$txt_start = "سلام، من ربات فست فود ` آزمایشی _name_ ` هستم.\n چه کاری می خواهید انجام دهید؟";

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
		// get location address from http://www.gps-coordinates.net/
		$txt_caption = "_name_ \n". "_fullName_ با بهترین خدمات و کادر مجرب در خدمت شماست.";
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
				// 'photo'  => new \CURLFile(realpath("static/images/telegram/germile/about.jpg")),
				'photo'  => 'AgADBAADrKcxG0lI6Aya2-pZtMoVCihvWBkABM8a9KIiiEpApgUAAgI',
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
				'address'   => '#83, Moallem 10, Qom, Iran +982537735183',
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