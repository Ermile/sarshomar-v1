<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class step_subscribe
{
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_text = null)
	{
		step::start('subscribe');
		// if is not set yet!
		$currentStatus = self::getSubscribe(true);
		if($currentStatus === null)
		{
			return self::step1($_text);
		}
		else
		{
			return self::step2($currentStatus, $_text);
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
				["No، تمایلی ندارم"],
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


	private static function getSubscribe($_boolResult = true)
	{
		$user_id = bot::$user_id;
		$qry =
		"SELECT * FROM options
			WHERE
				user_id = $user_id AND
				option_cat = 'subscribe_$user_id' AND
				option_key = 'telegram'
			LIMIT 1
			";

		$result = \lib\db::get($qry, 'option_status', true);
		if($_boolResult && is_string($result))
		{
			switch ($result)
			{
				case 'enable':
					$result = true;
					break;

				case 'disable':
					$result = false;
					break;

				case 'expire':
				default:
					$result = null;
					break;
			}
		}
		else
		{
			$result = null;
		}
		return $result;
	}


	/**
	 * save user subscribe into db
	 * @param  [type] $_status [description]
	 * @return [type]            [description]
	 */
	private static function saveSubscribe($_status = true)
	{
		// set status
		if($_status)
		{
			$_status = 'enable';
		}
		else
		{
			$_status = 'disable';
		}
		$meta       =
		[
			'time'   => date('Y-m-d H:i:s'),
			'status' => $_status,
		];
		$userDetail =
		[
			'cat'    => 'subscribe_'.bot::$user_id,
			'key'    => 'telegram',
			'value'  => 'status',
			'meta'   => $meta,
		];
		// set user_id
		if(isset(bot::$user_id))
		{
			$userDetail['user']   = bot::$user_id;
		}
		$userDetail['status'] = $_status;

		// save in options table
		\lib\utility\option::set($userDetail, true);
	}
}
?>