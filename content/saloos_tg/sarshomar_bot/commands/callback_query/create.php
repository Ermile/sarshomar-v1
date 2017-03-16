<?php
namespace content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use content\saloos_tg\sarshomar_bot\commands\make_view;
use \lib\telegram\step;
use \content\saloos_tg\sarshomar_bot\commands\menu;

class create
{
	public static function start($_query, $_data_url)
	{
		if(count($_data_url) > 1)
		{
			$method = $_data_url[1];
			$return = self::$method($_query, $_data_url);
		}
		if(is_array($return))
		{
			return $return;
		}
		return [];
	}

	public static function home($_query = null, $_data_url = null){
		$txt_text = "📍 " . T_("عنوان سوال را وارد کنید");
		$txt_text .= "\n\n";
		$txt_text .= "✳ " . T_("برای لغو ثبت نظرسنجی در هر مرحله دستور /cancel را ارسال کنید");
		$txt_text .= "\n" . utility::tag(T_("Create new poll"));
		$result   =
		[
			'text'         => $txt_text,
			'reply_markup' => [
				"remove_keyboard" => true
			]
		];
		return $result;
	}

	public static function upload_file($_query = null, $_data_url = null)
	{
		$make = new make_view(session::get('poll'));
		$make->message->add_title();
		$make->message->add('status', "\n" . "📍📍 " . T_("محتوای چند رسانه‌ای شامل عکس، فیلم، صوت یا فایل را وارد کنید"));
		$make->message->add('tag', utility::tag(T_("Create new poll")));
		$make->inline_keyboard->add([
				[
					"text" => T_("فایل ندارم"),
					"callback_data" => 'create/choise_type',
				],
				[
					"text" => T_("Cancel"),
					"callback_data" => 'create/cancel'
				]
			]);
		$return = $make->make();
		$return["response_callback"] = utility::response_expire('create');
		if($_query)
		{
			session::remove_back('expire', 'inline_cache', 'create');
			step::plus();
			callback_query::edit_message($make->make());
			return [];
		}
		return $return;
	}

	public static function type($_query, $_data_url)
	{
		session::set('poll_options' , 'type', $_data_url[2]);
		session::remove_back('expire', 'inline_cache', 'create');
		$poll_request = ['id' => session::get('poll'), 'answers' => [["type" => $_data_url[2]]]];
		if($_data_url[2] == 'like')
		{
			$poll_request['answers'][0]['title'] = T_("Do you like it!");
		}
		elseif($_data_url[2] == 'descriptive')
		{
			$poll_request['answers'][0]['title'] = T_("Please type your answer");
		}
		utility::make_request($poll_request);

		$poll_type_change = \lib\main::$controller->model()->poll_add(['method' => 'patch']);
		step::stop();

		$step = 'create_' . $_data_url[2];
		step::start($step);

		$step_class = '\content\saloos_tg\sarshomar_bot\commands\step_' . $step;

		callback_query::edit_message($step_class::step1());
	}


	public static function cancel($_query = null, $_data_url = null)
	{
		step::stop();
		step::start('cancel');
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		callback_query::edit_message(\content\saloos_tg\sarshomar_bot\commands\step_cancel::step1());
		return [];
	}

	public static function preview($_query = null, $_data_url = null)
	{
		step::stop();
		step::start('create_preview');
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		callback_query::edit_message(\content\saloos_tg\sarshomar_bot\commands\step_create_preview::step1());
		return [];
	}

	public static function save($_query = null, $_data_url = null)
	{
		step::stop();
		\content\saloos_tg\sarshomar_bot\commands\step_cancel::step2(true);
		return [];
	}

	public static function delete($_query = null, $_data_url = null)
	{
		step::stop();
		\content\saloos_tg\sarshomar_bot\commands\step_cancel::step2(false);
		return [];
	}
}
?>