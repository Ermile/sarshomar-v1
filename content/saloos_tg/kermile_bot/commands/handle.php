<?php
namespace content\saloos_tg\kermile_bot\commands;
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
			case 'cancel':
			case '/stop':
			case 'menu':
			case 'main':
			case 'mainmenu':
			case 'منو':
				$response = menu::main();
				break;

			case '/order':
			case 'order':
			case 'سفارش':
			case 'ثبت سفارش':
			case 'ثبت':
				$response = step_order::start();
				break;

			case 'ثبت':
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

			case '/feedback':
			case 'feedback':
			case 'ثبت':
			case 'ثبت بازخورد':
				$response = step_feedback::start();
				break;

			case 'return':
			case 'بازگشت':
				switch ($_cmd['text'])
				{
					case 'بازگشت به ثبت سفارش':
						$response = menu::polls();
						break;

					case 'بازگشت به منوی اصلی':
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