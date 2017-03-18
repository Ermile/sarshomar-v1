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

class step_create_advance
{
	public static function start($_text = null, $_run_as_edit = false)
	{
		step::start('create_advance');
		return self::step1();
	}


	public static function step1($_text = null)
	{
		$poll_id = session::get('poll');
		$maker = new make_view($poll_id);
		$maker->message->add_title();
		$maker->message->add_poll_list(null, false);
		if($maker->query_result['description'])
		{
			$maker->message->add('description', "📝 " . $maker->query_result['description']);
		}
		$maker->message->add('alert-description', "\n📝 ".T_("چنانکه نیاز به ارایه‌ مقاله، خبر و اطلاعات تکمیلی در مورد سوال ثبت‌شده و نحوه پاسخ‌دهی دارید، دکمه توضیح را بفشارید."));
		$maker->inline_keyboard->add([
			[
				'text' => "📝 " . T_('Description'),
				"callback_data" => 'create_advance/description'
			]
			]);

		$maker->message->add('privacy', "⚠ ".T_("در حالت پیش‌فرض، به‌دلیل رعایت حریم‌خصوصی، هویت پاسخ‌دهندگان مخفی است. چنانکه نیازمند مشاهده نام و نام‌کاربری پاسخ‌دهندگان هستید دکمه نیازمند مشخصات را بفشارید."));
		if(isset($maker->query_result['access_profile']))
		{
			$maker->inline_keyboard->add([
				[
					'text' => T_('پنهان‌سازی پاسخ‌دهنده'),
					"callback_data" => 'create_advance/access_profile/remove'
				]
				]);
		}
		else
		{

			$maker->inline_keyboard->add([
				[
					'text' => "⚠ " . T_('شناسایی پاسخ‌دهنده'),
					"callback_data" => 'create_advance/access_profile/add'
				]
				]);
		}
		$maker->inline_keyboard->add([
			[
				'text' => T_('Back'),
				"callback_data" => 'create/preview'
			]
			]);
		$return = $maker->make();
		$return["response_callback"] = utility::response_expire('create');
		return $return;
	}

	public static function step2($text = null, $substep = null)
	{
		$poll_id = session::get('poll');
		if($substep == 'remove')
		{
			utility::make_request(['id' => $poll_id, 'description' => null]);
			main::$controller->model()->poll_add(['method' => 'patch']);
			step::goingto(1);
			return self::step1();
		}
		elseif($text)
		{
			utility::make_request(['id' => $poll_id, 'description' => $text]);
			main::$controller->model()->poll_add(['method' => 'patch']);
			step::goingto(1);
			return self::step1();
		}
		$maker = new make_view($poll_id);

		if($maker->query_result['description'])
		{
			$maker->message->add('description', "📍 " . $maker->query_result['description']);

			$maker->inline_keyboard->add([
				[
					'text' => T_('remove description'),
					"callback_data" => 'create_advance/description/remove'
				]
				]);
		}

		$maker->message->add('alert', "\n📝 " . T_('شما می‌توانید مقاله و توضیحات تکمیلی مرتبط با سوال ثبت‌شده را در این قسمت وارد کنید. این اطلاعات جهت آگاهی‌دادن بیشتر به مخاطب و بازخوردگیری مناسب نسبت به سوال طرح شده شما می‌باشد.'));

		$maker->inline_keyboard->add([
			[
				'text' => T_('Back'),
				"callback_data" => 'create/advance'
			]
			]);

		$return = $maker->make();
		$return["response_callback"] = utility::response_expire('create');
		return $return;
	}

	public static function error()
	{
		debug::$status = 1;
		step::stop();
		return [
			'text' => debug::compile()['messages']['error'][0]['title'],
			'reply_markup' => menu::main(true)
		];
	}

}
?>