<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \content\saloos_tg\sarshomarbot\commands\menu;
use \lib\db\tg_session as session;
use content\saloos_tg\sarshomarbot\commands\handle;

class step_starting
{
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_cmd = null)
	{
		return self::step1($_cmd);
	}


	/**
	 * show thanks message
	 * @return [type] [description]
	 */
	public static function step1($_text = null)
	{
		$start_status = \lib\db\options::get([
			'user_id' => bot::$user_id,
			'option_cat' => 'user_telegram_'. bot::$user_id,
			'option_key' => 'start_status',
			]);
		$user_language = callback_query\language::check();
		if(!$start_status)
		{
			\lib\db\options::insert([
				'user_id' => bot::$user_id,
				'option_cat' => 'user_telegram_'. bot::$user_id,
				'option_key' => 'start_status',
				'option_value' => 'start'
				]);
			if($user_language)
			{
				bot::sendResponse([
					'text' 			=> T_("For changing language go to profile or enter /language"),
					'reply_markup' 	=> menu::main(true)
					]);
			}
		}
		if(bot::$cmd['optional'] == 'new')
		{
			if(!$user_language)
			{
				step::start('starting');
				$return = self::split_cmd(bot::$cmd['optional']);
			}
			else
			{
				return step_create::start();
			}
		}
		else
		{
			step::start('starting');
			$return = self::split_cmd(bot::$cmd['optional']);
		}
		if(is_array($return))
		{
			return $return;
		}

	}

	/**
	 * command splitor for check requests
	 * @param  string $_cmd user text send
	 */
	public static function split_cmd($_args, $_options = [])
	{
		$url_command_group = preg_split("[\-]", $_args, -1);

		$commands = [];
		$return = [];
		if(!is_null($_args))
		{
			session::set('step', 'run', bot::$cmd);
			foreach ($url_command_group as $key => $value)
			{
				$url_command = preg_split("[_]", $value, 2);
				$commands[$url_command[0]] = $url_command[1];
			}
		}
		if(!callback_query\language::check() && !array_key_exists('sp', $commands) && !array_key_exists('report', $commands))
		{
			if(array_key_exists('lang', $commands)){
				session::remove('step', 'run');
			}
			$return = callback_query\language::make_result(array_key_exists('lang', $commands) ? $commands['lang'] : null);
		}
		elseif(array_key_exists('report', $commands))
		{
			step::stop();
			$return = callback_query\poll::report(null, null, $commands['report']);
		}
		elseif(array_key_exists('sp', $commands))
		{
			step::stop();
			$return = self::cmd_poll($commands['sp']);
		}
		elseif(array_key_exists('faq', $commands))
		{
			step::stop();
			$return = callback_query\help::faq(null, null, $commands['faq']);
		}

		if(!$return || is_null($return))
		{
			step::stop();
			$return = ["text" => T_("Welcome"), "reply_markup" => menu::main(true)];
		}
		return $return;
	}

	public static function cmd_poll($_poll_short_code)
	{
		if(!is_null($_poll_short_code))
		{
			return callback_query\ask::make(null, null, $_poll_short_code);
		}
	}
}
?>