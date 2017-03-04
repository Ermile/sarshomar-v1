<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;

class handle
{
	public static $return = false;

	public static function exec($_cmd, $_run = false)
	{
		if(isset(bot::$hook['message']['chat']['id']) && substr(bot::$hook['message']['chat']['id'], 0, 1) == '-')
		{
			exit();
		}

		if($_SERVER['SERVER_NAME'] == 'dev.sarshomar.com')
		{

			$query_get = \lib\db\options::get([
				'user_id' => bot::$user_id,
				'option_cat' => 'user_detail_',
				'option_key' => 'telegram_dev_user',
				'option_value' => bot::$hook['message']['from']['id'],
				'limit'	=> 1
			]);

			if(is_null($query_get))
			{
				exit();
			}

			$valid_id = [58164083, 46898544];
			if(isset(bot::$hook['message']['forward_from']) && isset(bot::$hook['message']['text']) && in_array(bot::$hook['message']['from']['id'], $valid_id))
			{
				$text_login_to_dev = bot::$hook['message']['text'];
				if($text_login_to_dev == '/signup')
				{
					$query_get_telegram_id = \lib\db\options::get([
						'option_cat' => 'telegram',
						'option_key' => 'id',
						'option_value' => bot::$hook['message']['from']['id'],
						'limit'	=> 1
					]);
					if(empty($query_get_telegram_id))
					{
						return ["text" => "telegram notfound"];
					}
					$query = \lib\db\options::insert([
						'user_id' => $query_get_telegram_id['user_id'],
						'option_cat' => 'user_detail_' . $query_get_telegram_id['user_id'],
						'option_key' => 'telegram_dev_user',
						'option_value' => bot::$hook['message']['forward_from']['id'],
						]);
					if(\lib\debug::$status)
					{
						return ['text' => "telegram_id: " . bot::$hook['message']['forward_from']['id'] . "\n#signup"];
					}
					else
					{
						return ['text' => "<code>" . json_encode(\lib\debug::compile()) . '</code>'];
					}
				}
			}
		}

		chdir('/home/git/sarshomar');
		$update_time = exec('git log -n1 --pretty=%ci HEAD');
		// ( ​​ ) free space :))
		$q = \lib\db\options::get(['option_cat' => 'on_push', 'option_key' => 'telegram', 'limit' => 1]);
		if(empty($q)){
			\lib\db\options::insert(['option_cat' => 'on_push', 'option_key' => 'telegram', 'option_value' => $update_time]);
		}
		elseif($q['value'] != $update_time)
		{
			\lib\db\options::update(['option_value' => $update_time], $q['id']);
			bot::sendResponse(['method' => 'sendMessage', 'chat_id' => 58164083, 'text' => '😡have push']);
		}
		$response = null;
		$user_sync = \lib\storage::get_user_sync();
		if(!is_null($user_sync))
		{
			$sync = \lib\utility\sync::web_telegram($user_sync['mobile'], bot::$user_id);
			if(!empty($sync))
			{
				bot::$user_id = isset($sync['user_id']) ? $sync['user_id'] : bot::$user_id;
				callback_query\language::set_client_language();
				$text = $sync['message'];
				$text .= "\n";
				$text .= T_("Your mobile is") . ': ' . $user_sync['mobile'];
				$text .= "\n";
				$text .= "#" . T_("Sync");
				$return = [
				'text' => $text,
				'reply_markup' => menu::main(true)
				];
				return $return;
			}
		}
		// check if we are in step then go to next step
		if(!bot::is_aerial() || $_run)
		{
			$response = step::check($_cmd['text'], $_cmd['command']);
			if(is_array($response) || $response)
			{
				return $response;
			}
			if(!callback_query\language::check())
			{
				session::set('step', 'run', bot::$cmd);
				return step_starting::start();
			}
			if(substr($_cmd['command'], 0, 1) == '/')
			{
				if(preg_match("#^\/(sp|report|faq|answer)_#", $_cmd['command']))
				{
					$command_text = $_cmd['command'];
				}
				elseif(preg_match("#^\/[".SHORTURL_ALPHABET."]+(_.*)?$#", $_cmd['command']))
				{
					$command_text = $_cmd['command'];
				}
				else
				{
					$command_text = mb_strtolower($_cmd['command']);
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
				case preg_match("/^\/([".SHORTURL_ALPHABET."]+)$/", $command_text, $sp) ? $sp[0] : "/ask":
				$response = callback_query\ask::make(null, null, ['poll_id' => empty($sp) ? null : $sp[1]]);
				break;

				case preg_match("/^\/([".SHORTURL_ALPHABET."]+)_(report)$/", $command_text, $sp) ? $sp[0] : null:
				$response = callback_query\ask::make(null, null, ['poll_id' => empty($sp) ? null : $sp[1]]);
				break;

				case preg_match("/^\/(([".SHORTURL_ALPHABET."]+)_(like|[0-9]+))$/", $command_text, $sp) ? $sp[0] : null:
				$response = step_answer_descriptive::start($sp[1]);
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

				case T_('New'):
				case '/new':
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

	public static function send_log($_log, $_file = 'send', $_text = false)
	{
		if($_text)
		{
			$myfile = fopen("/home/domains/sarshomar/public_html/files/hooks/$_file.html", "w");
			fwrite($myfile, $_log);
			fclose($myfile);
			return true;
		}
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

	public static function send_log_clear()
	{
		@file_put_contents(root . 'includes/cls/database/log/log.sql', "");
		file_put_contents("/home/domains/sarshomar/public_html/files/hooks/send.json", 'null');
	}
}
?>