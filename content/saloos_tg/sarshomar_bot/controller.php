<?php
namespace content\saloos_tg\sarshomar_bot;
// use telegram class as bot
ini_set('display_errors'        , 'On');
ini_set('display_startup_errors', 'On');
ini_set('error_reporting'       , 'E_ALL | E_STRICT');
ini_set('track_errors'          , 'On');
ini_set('display_errors'        , 1);
error_reporting(E_ALL);
use \lib\telegram\tg as bot;
use \lib\db\tg_session as session;
use content\saloos_tg\sarshomar_bot\commands\handle;
class controller extends \lib\mvc\controller
{
	/**
	 * allow telegram to access to this location
	 * to send response to our server
	 * @return [type] [description]
	 */
	public static $microtime_log;
	public static $last_message = false;
	public static $callback_query_id_db = [];
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
				handle::send_log(json_encode(error_get_last()), 'error');
			}
		});
		set_error_handler(function(...$_args) {
			\lib\db::log($_args, null, 'telegram.json', 'json');
			handle::send_log($_args);
			self::$microtime_log[] = $_args;
		});

		$myhook = 'saloos_tg/sarshomar_bot/'.\lib\option::social('telegram', 'hookFolder');
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
			if(isset($_args['callback_query_id']))
			{
				if(in_array($_args['callback_query_id'], self::$callback_query_id_db))
				{
					$_args = [];
					return;
				}
				else
				{
					self::$callback_query_id_db[] = $_args['callback_query_id'];
				}
			}
			if(count($_args) < 2)
			{
				$_args = [];
				return;
			}
			if(!isset($_args['method']))
			{
				$method = $_name;
			}
			else
			{
				$method = $_args['method'];
			}
			if(isset($_args['method']) && $_args['method'] == 'sendimage')
			{
				$_args['method'] = 'sendphoto';
			}
			if(isset($_name) && $_name == 'sendimage')
			{
				$_name = 'sendphoto';
			}
			if(isset($_args['image']))
			{
				$_args['photo'] = $_args['image'];
				if(isset($_args['photo']->postname))
				{
					$_args['photo']->postname = 'photo';
				}
				unset($_args['image']);
			}
			$method = strtolower($method);
			$replay_markup_id = commands\utility::replay_markup_id();
			$replay_markup_id($_name, $_args);
			if($_SERVER['SERVER_NAME'] == 'dev.sarshomar.com' && $method != 'answercallbackquery')
			{
				if(isset($_args['results']))
				{
					foreach ($_args['results'] as $key => $value) {
						if(!isset($_args['results'][$key]['input_message_content']['message_text']))
						{
							continue;
						}
						$_args['results'][$key]['input_message_content']['message_text'] .= "\n💣" . commands\utility::tag(T_("Developer mode"));
						$_args['results'][$key]['input_message_content']['parse_mode'] = "HTML";
					}
				}
				else
				{
					if(isset($_args['text']) && $_args['text'] != "")
					{
						$_args['text'] = preg_replace("#\n.*\#" . str_replace(" ", "_", T_("Developer mode")) . "$#", "", $_args['text']);
						$_args['text'] .= "\n💣" . commands\utility::tag(T_("Developer mode"));
						$_args['parse_mode'] = "HTML";
					}
				}
			}
			if(!isset($_args['results']))
			{
				$_args['parse_mode'] = "HTML";
			}

			$last_micro_time = time();
			if(self::$last_message !== false && in_array(strtolower($method), ['sendmessage', 'editmessagetext', 'editmessagereplymarkup', 'editmessagecaption']))
			{
				if($last_micro_time - self::$last_message < 1)
				{
					sleep(1);
				}
			}
		};
		bot::$methods['after']["/.*/"] = bot::$methods['after']["/.*/"] = commands\utility::callback_session();

		/**
		 * start hooks and run telegram session from db
		 */
		bot::hook();
		if(!bot::$user_id)
		{
			echo "user not found";
			exit();
		}
		if(isset(bot::$hook['edited_message']))
		{
			exit();
		}
		\lib\main::$controller->model()->user_id = (int) bot::$user_id;
		\lib\main::$controller->model()->set_api_permission((int) bot::$user_id);

		$language = \lib\utility\users::get_language((int) bot::$user_id);
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
		if(!bot::$user_id)
		{
			$req = null;
			// check apache request header and use if exist
			if(function_exists('apache_request_headers'))
			{
				$req = apache_request_headers();
			}

			$log = [
			'::ERROR::' => "---------------",
			'request' => file_get_contents('php://input'),
			'apache' => $req,
			'hook' => bot::$hook,
			'debug' => \lib\debug::compile()
			];
			\lib\db::log($log, null, 'telegram-error.json', 'json');
		}
		\lib\db\tg_session::start(bot::$user_id);
		if(\lib\db\tg_session::get('last_message'))
		{
			self::$last_message = (int) \lib\db\tg_session::get('last_message');
		}

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
		\lib\db\tg_session::set('last_message', self::$last_message);
		\lib\db\tg_session::save();
		if(\lib\option::social('telegram', 'debug'))
		{
			var_dump($result);
		}
		exit();
	}

	public static function clear_back_temp()
	{
		$get_back_response = session::get_back('expire', 'inline_cache');
		if($get_back_response && \lib\storage::get_disable_edit() !== true)
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
				$rm = ['parse_mode', 'disable_web_page_preview', 'text', 'caption'];
				foreach ($rm as $key => $value) {
					if(isset($edit_return[$value]))
					{
						unset($edit_return[$value]);
					}
				}
				$edit_return['method'] = 'editMessageReplyMarkup';
				$x = bot::sendResponse($edit_return);
			}
		}
	}
}
?>