<?php
namespace content\saloos_tg\sarshomar_bot;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\db\tg_session as session;
use content\saloos_tg\sarshomar_bot\commands\handle;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
class controller extends \lib\mvc\controller
{
	/**
	 * allow telegram to access to this location
	 * to send response to our server
	 * @return [type] [description]
	 */
	public static $microtime_log;
	function _route()
	{
		ini_set('session.gc_maxlifetime', 3600 * 24 * 365);
		session_set_cookie_params(3600 * 24 * 365);
		handle::send_log_clear();
		register_shutdown_function(function()
		{
			if(!empty(self::$microtime_log))
			{
				handle::send_log(['mt_' . microtime(true) => self::$microtime_log], 'error');
			}
			else
			{
				@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/error.json", json_encode(error_get_last()));
			}
		});
		set_error_handler(function(...$_args) {
			self::$microtime_log[] = $_args;
		});

		$myhook = 'saloos_tg/sarshomar_bot/'.\lib\utility\option::get('telegram', 'meta', 'hookFolder');
		if($this->url('path') != $myhook)
		{
			return;
		}
		bot::$api_key     = '186535040:AAGKVOlmlpA4wU0Vjv0-s93w_o2aB3n0xKE';
		bot::$name        = 'sarshomar_bot';
		bot::$cmdFolder   = '\\'. __NAMESPACE__ .'\commands\\';
		bot::$defaultMenu = function(){
			return commands\menu::main(true);
		};
		bot::$once_log	  = false;
		bot::$methods['before']["/.*/"] = function(&$_name, &$_args)
		{
			$replay_markup_id = commands\utility::replay_markup_id();
			$replay_markup_id($_name, $_args);
			if($_SERVER['SERVER_NAME'] == 'dev.sarshomar.com' && $_args['method'] != 'answerCallbackQuery')
			{
				if(isset($_args['results']))
				{
					foreach ($_args['results'] as $key => $value) {
						$_args['results'][$key]['input_message_content']['message_text'] .= "\n⚠️" . commands\utility::tag(T_("Developer mode"));
						$_args['results'][$key]['input_message_content']['parse_mode'] = "HTML";
					}
				}
				else
				{
					if(isset($_args['text']) && $_args['text'] != "")
					{
						$_args['text'] = preg_replace("#\n.*\#" . str_replace(" ", "_", T_("Developer mode")) . "$#", "", $_args['text']);
						$_args['text'] .= "\n⚠️" . commands\utility::tag(T_("Developer mode"));
						$_args['parse_mode'] = "HTML";
					}
				}
			}
		};
		bot::$methods['after']["/.*/"] = bot::$methods['after']["/.*/"] = commands\utility::callback_session();
		// bot::$methods['after']["/.*/"] = bot::$methods['after']["/^.*$/"] = function($_name, $_args, $_return)
		// {
		// 	if(isset($_args['disable_after']))
		// 	{
		// 		return;
		// 	}
		// 	if(!isset($_return['ok']) || (isset($_return['ok']) && !$_return['ok']))
		// 	{
		// 		// $x = bot::sendResponse([
		// 		// 	'method' => 'sendMessage',
		// 		// 	'chat_id' => 58164083,
		// 		// 	'disable_after' => true,
		// 		// 	'text' => $text,
		// 		// 	'parse_mode' 				=> 'HTML',
		// 		// 	'disable_web_page_preview' 	=> true,
		// 		// ]);
		// 		$to      = 'itb.baravak@gmail.com';
		// 		$subject = 'Telegram Error';
		// 		$message = "<code>";
		// 		$message .= "\n" . json_encode($_return, JSON_UNESCAPED_UNICODE);
		// 		$message .= "\n" . json_encode($_args, JSON_UNESCAPED_UNICODE);
		// 		$message .= "\n" . json_encode(bot::$hook, JSON_UNESCAPED_UNICODE);
		// 		$message .= "</code>";


		// 		// To send HTML mail, the Content-type header must be set
		// 		$headers[] = 'MIME-Version: 1.0';
		// 		$headers[] = 'Content-type: text/html; charset=utf8';

		// 		// Additional headers
		// 		$headers[] = 'To: Hasan Salehi <itb.baravak@gmail.com>';
		// 		$headers[] = 'From: Hasan Salehi <itb.baravak@gmail.com>';

		// 		// Mail it
		// 		mail($to, $subject, $message, implode("\r\n", $headers));
		// 	}
		// };

		/**
		 * start hooks and run telegram session from db
		 */
		bot::hook();
		\lib\main::$controller->model()->user_id = (int) bot::$user_id;
		\lib\main::$controller->model()->set_api_permission((int) bot::$user_id);

		$language = \lib\db\users::get_language((int) bot::$user_id);
		if(empty($language) || !$language)
		{
			\lib\define::set_language('en_US');
		}
		else
		{
			\lib\define::set_language($language, true);
		}
		// exit();
		bot::$fill        =
		[
		'name'     => T_('Sarshomar'),
		'fullName' => T_('Sarshomar'),
			// 'about'    => $txt_about,
		];
		bot::$defaultText = T_('Undefined');
		\lib\db\tg_session::$user_id = bot::$user_id;
		\lib\db\tg_session::start();
		$_SESSION['tg'] = \lib\db\tg_session::get_back('tg') ? \lib\db\tg_session::get_back('tg') : [];
		$_SESSION['tg'] = commands\utility::object_to_array($_SESSION['tg']);

		/**
		 * run telegram handle
		 */
		$result           = bot::run(true);

		$after_run = \lib\storage::get_after_run();
		if($after_run){
			if(is_object($after_run))
			{
				call_user_func_array($after_run, []);
			}
			else
			{
				call_user_func_array($after_run[0], array_slice($after_run, 1));
			}
		}


		self::clear_back_temp();


		if(!\lib\storage::get_current_command())
		{
			session::remove('expire', 'command');
		}

		/**
		 * save telegram sessions to db
		 */
		\lib\db\tg_session::set('tg', $_SESSION['tg']);
		\lib\db\tg_session::save();
		if(\lib\utility\option::get('telegram', 'meta', 'debug'))
		{
			var_dump($result);
		}
		exit();
	}

	public static function clear_back_temp()
	{
		$get_back_response = session::get_back('expire', 'inline_cache');
		if($get_back_response && !\lib\storage::get_disable_edit())
		{
			foreach ($get_back_response as $key => $value) {
				$edit_return = commands\utility::object_to_array($value->on_expire);
				$get_original = session::get('expire', 'inline_cache', $key);
				$callback_query = (array) session::get('tmp', 'callback_query');
				$callback_session = array_search($edit_return['message_id'], $callback_query);
				if($callback_session !== false)
				{
					unset($callback_query[$callback_session]);
					session::set('tmp', 'callback_query', $callback_query);
				}
				if($value->save_unique_id == $get_original->save_unique_id)
				{
					session::remove('expire', 'inline_cache', $key);
				}

				bot::sendResponse($edit_return);
			}
		}
	}
}
?>