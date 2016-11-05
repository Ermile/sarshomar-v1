<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;

class handle
{
	public static $return = false;

	public static function exec($_cmd)
	{
		bot::$defaultText = T_('Not Found');
		if($_cmd['command'] == 'exit')
		{
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/error.json", "null");
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/log.json", "null");
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/send.json", "null");
			$id = Tld === 'dev' ? 5 : 99;
			\lib\db::query("DELETE FROM options
				WHERE user_id = $id AND
				(option_cat = 'user_detail_{$id}' or option_cat = 'telegram')
				");
			\lib\db::query("DELETE from polldetails where 1");
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
				return $response;
			}
			if(substr($_cmd['command'], 0, 1) == '/')
			{
				if(substr($_cmd['command'], 0, 4) == '/sp_')
				{
					$command_text = $_cmd['command'];
				}
				else
				{
					$command_text = strtolower($_cmd['command']);
				}
			}
			else
			{
				$command_text = $_cmd['text'];
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

				case '/ask':
				case T_('Ask from me'):
				case preg_match("/^(\/sp_([^\s]+))$/", $command_text, $sp) ? $sp[1] : '/ask':
				$response = step_sarshomar::start(empty($sp) ? null : $sp[2]);
				break;



				case '/dashboard':
				case T_('Dashboard'):
				$response = step_dashboard::start();
				break;

				case '/help':
				case T_('Help'):
				$response = step_help::start();
				break;

				case '/faq':
				$response = step_help::exec($command_text);
				break;

				case '/commands':
				$response = step_help::exec($command_text);
				break;

				case '/feedback':
				$response = step_help::exec($command_text);
				break;

				case '/privacy':
				$response = step_help::exec($command_text);
				break;

				case '/about':
				$response = step_help::exec($command_text);
				break;

				case T_('Create new pool'):
				case T_('Create'):
				case '/create':
				$response = step_create::start();
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