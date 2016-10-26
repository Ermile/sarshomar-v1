<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class handle
{
	public static $return = false;

	public static function exec($_cmd)
	{
		// bot::$defaultText = T_('Not Found');
		if($_cmd['command'] == 'exit')
		{
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/error.json", "null");
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/log.json", "null");
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/send.json", "null");
			\lib\db::query("DELETE FROM options
				WHERE user_id = 99 AND
				(option_cat = 'user_detail_99' or option_cat = 'telegram')
				");
			session_destroy();
			bot::sendResponse(['method' => 'sendMessage', 'chat_id' => 58164083, 'text' => 'destroy']);
			exit();
		}
		if(file_exists("/home/domains/sarshomar/public_html/files/hooks/log.json"))
		{

			$file = file_get_contents("/home/domains/sarshomar/public_html/files/hooks/log.json");
			$json = json_decode($file, true);
			if(!is_array($json))
			{
				$json = [];
			}
			array_unshift($json, bot::$hook);
			file_put_contents("/home/domains/sarshomar/public_html/files/hooks/log.json", json_encode($json, JSON_UNESCAPED_UNICODE));
		}
		$response = null;
		// check if we are in step then go to next step
		if(!bot::is_aerial())
		{
			$response = step::check($_cmd['text'], $_cmd['command']);
			if($response)
			{
				self::send_log(['call' => $response]);
				return $response;
			}
			if(substr($_cmd['command'], 0, 1) == '/')
			{
				$command_text = strtolower($_cmd['command']);
			}
			else
			{
				$command_text = strtolower($_cmd['text']);
			}
			switch ($command_text)
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

				case '/ask':
				case T_('ask from me'):
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

				case T_('create new pool'):
				case '/create':
				$response = step_create::start();
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

				case '/feedback':
				case 'feedback':
				case 'ثبت':
				case 'ثبت بازخورد':
				step::set('menu', menu::main(true));
				$response = \lib\telegram\commands\step_feedback::start();
				break;

				case 'return':
				case 'back':
				case T_('🔙 Back'):
				case 'بازگشت':
				switch ($_cmd['text'])
				{
					case 'بازگشت به منوی نظرسنجی‌ها':
					$response = menu::polls();
					break;

					case T_('🔙 Back'):
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
		}
		elseif(array_key_exists('inline_query', bot::$hook))
		{
			$response = inline_query::start(bot::$hook['inline_query']);
		}
		elseif(array_key_exists('callback_query', bot::$hook))
		{
			$response = callback_query::start(bot::$hook['callback_query']);
		}
		elseif(array_key_exists('chosen_inline_result', bot::$hook))
		{
			$response = chosen_inline_result::start(bot::$hook['chosen_inline_result']);
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
		self::send_log($response);
		return $response;
	}

	public static function send_log($_log)
	{
		if(file_exists("/home/domains/sarshomar/public_html/files/hooks/send.json"))
		{

			$file = file_get_contents("/home/domains/sarshomar/public_html/files/hooks/send.json");
			$json = json_decode($file, true);
			if(!is_array($json))
			{
				$json = [];
			}
			array_unshift($json, $_log);
			file_put_contents("/home/domains/sarshomar/public_html/files/hooks/send.json", json_encode($json, JSON_UNESCAPED_UNICODE));
		}
	}
}
?>