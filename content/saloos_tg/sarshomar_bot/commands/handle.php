<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;
use \lib\utility\telegram\step;

class handle
{
	public static $return = false;

	public static function exec($_cmd)
	{
		$response = null;
		// check if we are in step then go to next step
		$response = step::check($_cmd['text'], $_cmd['command']);
		if($response)
		{
			return $response;
		}

		switch ($_cmd['command'])
		{
			case '/menu':
			case '/cancel':
			case '/stop':
			case 'menu':
			case 'main':
			case 'mainmenu':
			case 'منو':
				$response = menu::main();
				break;

			case '/poll':
			case 'poll':
			case '/polls':
			case 'polls':
			case 'شرکت':
			case 'شرکت در نظرسنجی':
				$response = menu::polls();
				break;

			case '/sarshomar':
			case 'sarshomar':
			case 'نظرسنجی‌های سرشمار':
				$response = step_sarshomar::start();
				break;

			case '/removeUserAnswers':
			case 'removeUserAnswers':
				$response = step_sarshomar::removeUserAnswers();
				break;

			case '/my':
			case 'my':
			case 'من':
				$response = menu_my::my();
				break;

			case '/mypolls':
			case 'mypolls':
				$response = menu_my::mypolls();
				break;

			case '/define':
			case 'define':
			case 'تعریف':
				$response = step_define::start();
				break;

			case 'نظرسنجی‌های':
				switch ($_cmd['text'])
				{
					case 'نظرسنجی‌های من':
						$response = menu_my::my();
						break;

					case 'نظرسنجی‌های موجود':
						$response = menu_my::mypolls();
						break;

					case 'نظرسنجی‌های سرشمار':
						$response = step_sarshomar::start();
						break;

					default:
						break;
				}
				break;

			case '/psychology':
			case 'psychology':
			case 'روانشناسی':
				$response = menu_psychology::psychology();
				break;

			case '/civility':
			case 'civility':
			case 'مردمی':
				$response = menu_civility::civility();
				break;

			case '/profile':
			case 'profile':
			case 'پروفایل':
				$response = menu_profile::profile();
				break;

			case 'return':
			case 'بازگشت':
				switch ($_cmd['text'])
				{
					case 'بازگشت به منوی نظرسنجی‌ها':
						$response = menu::polls();
						break;

					case 'بازگشت به منوی اصلی':
						$response = menu::main();
						break;

					default:
						$response = menu::main();
						break;
				}
				break;

			default:
				break;
		}

		// automatically add return to end of keyboard
		if(self::$return)
		{
			// if has keyboard
			if(isset($response['replyMarkup']['keyboard']))
			{
				$response['replyMarkup']['keyboard'][] = ['بازگشت'];
			}
		}

		return $response;
	}
}
?>