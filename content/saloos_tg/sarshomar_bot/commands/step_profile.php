<?php
namespace content\saloos_tg\sarshomar_bot\commands;

use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use \content\saloos_tg\sarshomar_bot\commands\markdown_filter;
use \content\saloos_tg\sarshomar_bot\commands\make_view;
use \content\saloos_tg\sarshomar_bot\commands\menu;
use \lib\main;
use \lib\debug;

class step_profile
{

	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('profile');
		return self::step1();
	}


	public static function step1($_text = null)
	{
		$user_profile = array_keys(\lib\main::$controller->model()->get_user_profile());
		$original_profile = array_keys(\lib\utility\profiles::profile_data());
		$uncomplate = array_values(array_diff($original_profile, $user_profile));
		handle::send_log($uncomplate);
		handle::send_log($user_profile);
		if(empty($uncomplate))
		{
			return false;
		}
		$method = $uncomplate[0];
		$return =  self::$method();
		$return["response_callback"] = utility::response_expire('profile');
		return $return;

	}

	public static function marrital()
	{
		return [
			'text' => T_("Marrital status"),
			'reply_markup' => [
				'inline_keyboard' => [
					[
						[
							'text' => T_("Single"),
							'callback_data' => 'profile/marrital/single'
						],
						[
							'text' => T_("Married"),
							'callback_data' => 'profile/marrital/married'
						]
					]
				]
			]
		];
	}

	public static function gender()
	{
		return [
			'text' => T_("Gender"),
			'reply_markup' => [
				'inline_keyboard' => [
					[
						[
							'text' => T_("Male"),
							'callback_data' => 'profile/gender/male'
						],
						[
							'text' => T_("Female"),
							'callback_data' => 'profile/gender/female'
						]
					]
				]
			]
		];
	}

	public static function graduation()
	{
		return [
			'text' => T_("Graduation"),
			'reply_markup' => [
				'inline_keyboard' => [
					[
						[
							'text' => T_("Illiterate"),
							'callback_data' => 'profile/graduation/illiterate'
						],
						[
							'text' => T_("Undergraduate"),
							'callback_data' => 'profile/graduation/undergraduate'
						],
						[
							'text' => T_("Graduate"),
							'callback_data' => 'profile/graduation/graduate'
						]
					]
				]
			]
		];
	}

	public static function degree()
	{
		return [
			'text' => T_("Degree"),
			'reply_markup' => [
				'inline_keyboard' => [
					[
						[
							'text' => T_("Under diploma"),
							'callback_data' => 'profile/degree/under diploma'
						],
						[
							'text' => T_("Diploma"),
							'callback_data' => 'profile/degree/diploma'
						],
						[
							'text' => T_("2 year college"),
							'callback_data' => 'profile/degree/2 year college'
						],
						[
							'text' => T_("Bachelor"),
							'callback_data' => 'profile/degree/bachelor'
						],
						[
							'text' => T_("Master"),
							'callback_data' => 'profile/degree/master'
						],
						[
							'text' => T_("PHD"),
							'callback_data' => 'profile/degree/phd'
						]
					]
				]
			]
		];
	}

	public static function range()
	{
		return [
			'text' => T_("Age range"),
			'reply_markup' => [
				'inline_keyboard' => [
					[
						[
							'text' => T_("-13"),
							'callback_data' => 'profile/range/-13'
						],
						[
							'text' => T_("14-17"),
							'callback_data' => 'profile/range/14-17'
						],
						[
							'text' => T_("18-24"),
							'callback_data' => 'profile/range/18-24'
						],
						[
							'text' => T_("25-30"),
							'callback_data' => 'profile/range/25-30'
						],
						[
							'text' => T_("31-44"),
							'callback_data' => 'profile/range/31-44'
						],
						[
							'text' => T_("45-59"),
							'callback_data' => 'profile/range/45-59'
						],
						[
							'text' => T_("60+"),
							'callback_data' => 'profile/range/60+'
						]
					]
				]
			]
		];
	}

	public static function employmentstatus()
	{
		return [
			'text' => T_("Employment status"),
			'reply_markup' => [
				'inline_keyboard' => [
					[
						[
							'text' => T_("Employee"),
							'callback_data' => 'profile/employmentstatus/employee'
						],
						[
							'text' => T_("Unemployed"),
							'callback_data' => 'profile/employmentstatus/unemployed'
						],
						[
							'text' => T_("Retired"),
							'callback_data' => 'profile/employmentstatus/retired'
						]
					]
				]
			]
		];
	}
}
?>