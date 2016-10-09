<?php
namespace content\saloos_tg\sarshomarbot\commands;
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
				$response = step_starting::start($_cmd);
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
	 * show about message
	 * @return [type] [description]
	 */
	public static function about()
	{
		$site_url = 'https://sarshomar.com';
		if(isset($_args['language']) && $_args['language'] == 'fa')
		{
			$site_url .= '/fa';
		}

		$txt_caption = "_name_\n";
		$txt_caption .= T_("Created in Ermile");

		$txt_text = "[_name_]($site_url) برای انجام آسان و سریع نظرسنجی‌های دقیق و به‌دور از شبهه در مقیاس وسیع و با هزینه مناسب طراحی شده است.\n";
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
				'text'                     => $txt_text,
				'disable_web_page_preview' => true,
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
				'title'     => 'Ermile',
				// 'address'   => 'ایران، قم، خیابان معلم۱۰، پلاک۸۳',
				'address'   => '#614, Omranieh, Moallem, Qom, Iran +982537735183',
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