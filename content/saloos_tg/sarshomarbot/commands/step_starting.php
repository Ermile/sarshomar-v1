<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \content\saloos_tg\sarshomarbot\commands\menu;
use content\saloos_tg\sarshomarbot\commands\handle;

class step_starting
{
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */

	static $valid_lang = [
		'fa_IR' => ['فارسی', 'fa', 'persian', 'farsi', 'fa_ir', 'fa-ir'],
		'en_US' => ['en', 'english']
	];
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
		if(bot::$cmd['optional'] == 'create')
		{
			return step_create::start();
		}
		step::start('starting');
		$return = self::split_cmd(bot::$cmd['optional']);
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
			foreach ($url_command_group as $key => $value)
			{
				$url_command = preg_split("[_]", $value, 2);
				$commands[$url_command[0]] = $url_command[1];
			}
		}
		if(array_key_exists('report', $commands))
		{
			step::stop();
			$return = callback_query\poll::report(null, null, $commands['report']);
		}
		elseif(array_key_exists('sp', $commands))
		{
			step::stop();
			$return = self::cmd_poll($commands['sp']);
		}elseif(!callback_query\language::check())
		{
			$return = callback_query\language::make_result(array_key_exists('lang', $commands) ? $commands['lang'] : null);
		}elseif(array_key_exists('faq', $commands))
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

	/**
	 * get user answer for subscribe status
	 * @param  [type] $_feedback [description]
	 * @return [type]            [description]
	 */
	public static function step3($_feedback, $_prefixText = null)
	{

		exit();


		$txt_text = $_prefixText;
		if(!is_bool($_feedback))
		{
			switch ($_feedback)
			{
				case 'بلع':
				case 'بله،':
				case 'بله، علاقمندم مشترک شوم':
				case '/yes':
				case 'yes':
				case '/y':
				case 'y':
				$txt_text = "پس از افزودن شدن نظرسنجی‌های جدید، شما به صورت خودکار مطلع خواهید شد:)\n";
break;

default:
$txt_text .= "به منوی اصلی بازگشتیم.\n";
break;
}
}
step::stop();

$result   =
[
'text'         => $txt_text,
'reply_markup' => menu::main(true),
];

return $result;
}

	/**
	 * start conversation
	 * @return [type] [description]
	 */
	public static function start_msg($_args = null)
	{
		$site_url = 'https://sarshomar.com';
		if(isset($_args['language']) && $_args['language'] == 'fa')
		{
			$site_url .= '/fa';
		}
		$txt_start = T_("Welcome to [_name_]($site_url)!");
		$txt_start .= "\n". T_("*Ask Anyone Anywhere*."). "\n\n";
		$txt_start .= T_("[_name_](https://sarshomar.com) is a modern service to give your precious opinion and help you to ask from anyone of you want."). "\n\n\n";
		$menu      = menu::main(true);
		// if language isset then do not show message for selecting language and show main menu
		if(!isset($_args['language']))
		{
			$txt_start .= T_("At first choose your language. You can change it later on settings.");
			$menu      = menu_language::set_one(true);
		}
		// $txt_start .= T_("To be continue...");
		$result =
		[
			// [
		'text'                     => $txt_start,
		'reply_markup'             => $menu,
		'disable_web_page_preview' => true,
			// ],
		];
		return $result;
	}



	/**
	 * save reference at start using bot
	 * @param  boolean $_status [description]
	 * @return [type]           [description]
	 */
	public static function saveRef($_ref)
	{
		$ref  = \lib\utility\shortURL::decode($_ref);
		$meta =
		[
		'time' => date('Y-m-d H:i:s'),
		'ref'  => $ref,
		'me'   => bot::$user_id,
		];
		// check if this is first time for this user
		$userDetail =
		[
		'user'   => $ref,
		'cat'    => 'ref_'.$ref,
		'key'    => 'telegram',
		'value'  => bot::$user_id,
		'meta'   => $meta,
		'stauts' => 'enable',
		];
		if(!bot::$user_id)
		{
			$userDetail['status'] = 'disable';
		}

		// save in options table
		$result = \lib\utility\option::set($userDetail, true);
		// reference is correct, save point for sender
		if($result)
		{

		}
	}
}
?>