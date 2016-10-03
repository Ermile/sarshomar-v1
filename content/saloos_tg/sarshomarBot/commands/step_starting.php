<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class step_starting
{
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_cmd = null)
	{
		step::start('starting');

		// generate response
		$_args = null;
		if(substr($_cmd['optional'], 0, 5) === 'poll_')
		{
			$opt = substr($_cmd['optional'], 5);
			// show related poll id

		}
		if(substr($_cmd['optional'], 0, 4) === 'ref_')
		{
			$opt = substr($_cmd['optional'], 4);
			// save this reference
			self::saveRef($opt);

		}
		if(substr($_cmd['optional'], 0, 5) === 'lang_')
		{
			$opt               = substr($_cmd['optional'], 5);
			$_args['language'] = $opt;
			// save this reference
			// set lang and skip asking new language step
			\lib\db\users::set_language($_args['language']);
		}
		$response = self::start($_args);



		// if is not set yet!
		$currentLanguage = self::getSubscribe(true);
		if($currentLanguage === null)
		{
			return self::step1($_text);
		}
		else
		{
			return self::step2($currentLanguage, $_text);
		}

	}


	/**
	 * show thanks message
	 * @return [type] [description]
	 */
	public static function step1($_text = null)
	{
		// go to next step
		step::plus();
		if(bot::$user_id)
		{
			// all is users!
		}
		// generate subscribe text
		$final_text = $_text;
		$final_text .= "آیا مایلید مشترک ما شده و پس از اضافه شدن نظرسنجی‌های جدید مطلع شوید؟\n";
		$menu =
		[
			'keyboard' =>
			[
				["بله، علاقمندم مشترک شوم"],
				["خیر، تمایلی ندارم"],
			],
		];
		// get name of question
		$result   =
		[
			'text'         => $final_text,
			'reply_markup' => $menu,
		];
		// return menu
		return $result;
	}


	/**
	 * get user answer for subscribe status
	 * @param  [type] $_feedback [description]
	 * @return [type]            [description]
	 */
	public static function step2($_feedback, $_prefixText = null)
	{
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
					self::saveSubscribe(true);
					break;

				default:
					$txt_text .= "به منوی اصلی بازگشتیم.\n";
					self::saveSubscribe(false);
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
	public static function start($_args = null)
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
		/**

		 * send message to sender to say thanks
		 */
		// $text   = "اولین کاربری که معرفی کردید در سیستم ثبت نام کرد:)\n";
		// $text   .= "به پاس قدردانی از حسن اعتمادتان حساب کاربری شما *100* امتیاز شارژ شد.";
		// $result =
		// [
		// 	'text'  => $text,
		// 	'chat' => '',
		// ];
		// bot::sendResponse($result);
	}
}
?>