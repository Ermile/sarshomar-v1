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
		// ( ​​ ) free space :))
		// handle::send_log(bot::$hook);
		// if(!preg_match("/^(99|5|22)$/", bot::$user_id)){
			// bot::sendResponse(['method' => 'sendMessage', 'chat_id' => 58164083, 'text' => 'salam [​](https://sarshomar.com/static/images/art/agencies.png)']);
		// 	exit();
		// }
		$q = \lib\db\options::get(['option_cat' => 'on_push', 'option_key' => 'telegram', 'limit' => 1]);
		if(empty($q)){
			\lib\db\options::insert(['option_cat' => 'on_push', 'option_key' => 'telegram', 'option_value' => filemtime(__FILE__)]);
		}
		elseif($q[0]['value'] != filemtime(__FILE__))
		{
			\lib\db\options::update(['option_value' => filemtime(__FILE__)], $q[0]['id']);
			bot::sendResponse(['method' => 'sendMessage', 'chat_id' => 58164083, 'text' => 'have push']);
		}
		bot::$defaultText = T_('Not Found');
		if($_cmd['command'] == 'exit' || $_cmd['command'] == '/exit')
		{
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/error.json", "null");
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/log.json", "null");
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/send.json", "null");
			@file_put_contents("/home/domains/sarshomar/public_html/files/db.log", "");
			$id = Tld === 'dev' ? 5 : 99;
			\lib\db::query("DELETE FROM options
				WHERE user_id = $id AND
				(option_cat = 'user_detail_{$id}' or option_cat = 'telegram')
				AND option_key <> 'id'
				");
			\lib\db::query("DELETE from polldetails where user_id = $id");
			\lib\db::query("DELETE from polldetails where user_id = 56");
			$id = 22;
			\lib\db::query("DELETE FROM options
				WHERE user_id = $id AND
				(option_cat = 'user_detail_{$id}' or option_cat = 'telegram')
				AND option_key <> 'id'
				");
			\lib\db::query("DELETE from polldetails where user_id = $id");
			session_destroy();
			bot::sendResponse(['method' => 'sendMessage', 'chat_id' => 58164083, 'text' => 'destroy: ']);
			exit();
		}
		elseif($_cmd['command'] == 'clear' || $_cmd['command'] == '/clear')
		{
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/error.json", "null");
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/log.json", "null");
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/send.json", "null");
			@file_put_contents("/home/domains/sarshomar/public_html/files/db.log", "");
			step::stop();
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
		$user_sync = \lib\storage::get_user_sync();
		if(!is_null($user_sync))
		{
			$sync = \lib\utility\sync::web_telegram($user_sync['mobile'], bot::$user_id);
			if($sync->is_ok())
			{
				$text = $sync->get_message();
				$text .= "\n";
				$text .= T_("Words Status change") . ': ' . $user_sync['mobile'];
				$text .= "\n";
				$text .= "#" . T_("Sync");
				return ['text' => $text];
			}
		}
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
				if(preg_match("#^\/(sp|report|faq)_#", $_cmd['command']))
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
				case T_("Back"):
				$response = menu::main();
				break;

				case '/ask':
				case T_('Ask me'):
				case preg_match("/^(\/sp_([^\s]+))$/", $command_text, $sp) ? $sp[1] : '/ask':
				$response = callback_query\ask::make(null, null, empty($sp) ? null : $sp[2]);
				break;

				case preg_match("/^(\/report_([^\s]+))$/", $command_text, $sp) ? $sp[1] : null:
				$response = callback_query\poll::report(null, null, empty($sp) ? null : $sp[2]);
				break;

				case '/polls':
				case T_('My polls'):
				$response = callback_query\poll::list(null, null);
				break;

				case T_("Language"):
				case '/language':
				$response = callback_query\language::make_result(null, true);
				break;

				case '/start':
				$response = step_starting::start($_cmd['text']);
				break;

				case '/dashboard':
				case T_('Dashboard'):
				$response = step_dashboard::start();
				break;

				case '/profile':
				case T_('Profile'):
				$response = step_dashboard::profile();
				break;

				case '/help':
				case T_('Help'):
				$response = step_help::start();
				break;

				case '/faq':
				$response = step_help::exec($command_text);
				break;

				case preg_match("/^(\/faq_([^\s]+))$/", $command_text, $faq) ? $faq[1] : '/faq':
				$response = callback_query\help::faq(null, null, $faq[2]);
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

				case T_('Create new poll'):
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
		if(!is_array($response))
		{
			$response = [];
		}
		return $response;
	}

	public static function send_log($_log, $_file = 'send')
	{
		if(file_exists("/home/domains/sarshomar/public_html/files/hooks/$_file.json"))
		{

			$file = file_get_contents("/home/domains/sarshomar/public_html/files/hooks/$_file.json");
			$json = json_decode($file, true);
			if(!is_array($json))
			{
				$json = [];
			}
			array_unshift($json, $_log);
			file_put_contents("/home/domains/sarshomar/public_html/files/hooks/$_file.json", json_encode($json, JSON_UNESCAPED_UNICODE));
		}
	}
}
?>