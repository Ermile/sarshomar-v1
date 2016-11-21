<?php
namespace content\saloos_tg\sarshomarbot;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\db\tg_session as session;
use content\saloos_tg\sarshomarbot\commands\handle;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
class controller extends \lib\mvc\controller
{
	public $custom_language = true;
	/**
	 * allow telegram to access to this location
	 * to send response to our server
	 * @return [type] [description]
	 */
	function _route()
	{
		if(isset($_GET['log']))
		{
			\lib\db\tg_session::$user_id = 99;
			\lib\db\tg_session::start();
			print_r(get_object_vars(\lib\db\tg_session::get()));
			exit();
		}
		register_shutdown_function(function()
		{
			@file_put_contents("/home/domains/sarshomar/public_html/files/hooks/error.json", json_encode(error_get_last()));
		});
		$myhook = 'saloos_tg/sarshomarbot/'.\lib\utility\option::get('telegram', 'meta', 'hookFolder');
		if($this->url('path') == $myhook)
		{
			bot::$api_key     = '142711391:AAFH0ULw7BzwdmmiZHv2thKQj7ibb49DJ44';
			bot::$name        = 'sarshomarbot';
			bot::$cmdFolder   = '\\'. __NAMESPACE__ .'\commands\\';
			bot::$defaultMenu = function(){
				return commands\menu::main(true);
			};
			bot::$once_log	  = false;
			bot::$methods['before']["/.*/"] = function(&$_name, &$_args){
				if(isset($_args['reply_markup']) && isset($_args['reply_markup']['inline_keyboard']))
				{
					$id = microtime(true);
					for ($i=0; $i < count($_args['reply_markup']['inline_keyboard']); $i++)
					{
						for ($j=0; $j < count($_args['reply_markup']['inline_keyboard'][$i]); $j++)
						{
							if(isset($_args['reply_markup']['inline_keyboard'][$i][$j]['callback_data']))
							{
								$_args['reply_markup']['inline_keyboard'][$i][$j]['callback_data'] = $id . ':' . $_args['reply_markup']['inline_keyboard'][$i][$j]['callback_data'];
							}
						}
					}
				}
			};

			/**
			 * start hooks and run telegram session from db
			 */
			bot::hook();
			$language = \lib\db\users::get_language(bot::$user_id);
			if(empty($language) || !$language)
			{
				\lib\define::set_language(self::$language);
			}
			else
			{
				\lib\define::set_language($language);
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


			$get_back_response = session::get_back('expire', 'inline_cache');
			if($get_back_response && !\lib\storage::get_disable_edit())
			{
				foreach ($get_back_response as $key => $value) {

					$edit_return = commands\utility::object_to_array($value->on_expire);
					$get_original = session::get('expire', 'inline_cache', $key);
					if($value->save_unique_id == $get_original->save_unique_id)
					{
						session::remove('expire', 'inline_cache', $key);
					}

					bot::sendResponse($edit_return);
				}
			}

			/**
			 * save telegram sessions to db
			 */
			\lib\db\tg_session::save();
			if(\lib\utility\option::get('telegram', 'meta', 'debug'))
			{
				var_dump($result);
			}
			exit();
		}
	}
}
?>