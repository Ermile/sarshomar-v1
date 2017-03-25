<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomarbot\commands\utility;
use content\saloos_tg\sarshomarbot\commands\make_view;
use \lib\telegram\step;
use \content\saloos_tg\sarshomarbot\commands\menu;

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
		$txt_text = "📍 " . T_("Enter question's title");
		$txt_text .= "\n\n";
		$txt_text .= "✳ " . T_("To cancel poll submission send /cancel command in each step.");
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

	public static function upload_file($_query = null, $_data_url = null, $_error = null)
	{
		$make = new make_view(session::get('poll'));
		$make->message->add_title();
		$make->message->add('status', "\n" . "📍📍 " . T_("Add multimedia content including image, movie, audio or file."));
		if(is_string($_error))
		{
			$make->message->add('error', "\n" . "⭕ " . $_error);
		}
		$make->message->add('tag', utility::tag(T_("Create new poll")));
		$make->inline_keyboard->add([
				[
					"text" => T_("I don't have file"),
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

		if($_data_url[2] == 'select' || $_data_url[2] == 'emoji')
		{
			$step = 'create_' . $_data_url[2];
			step::start($step);

			$step_class = '\content\saloos_tg\sarshomarbot\commands\step_' . $step;
			callback_query::edit_message($step_class::step1());
		}
		else
		{
			return self::preview();
		}


	}

	public static function choise_type($_query = null, $_data_url = null)
	{
		step::goingto(4);
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		callback_query::edit_message(\content\saloos_tg\sarshomarbot\commands\step_create::step4());
		return [];
	}

	public static function cancel($_query = null, $_data_url = null)
	{
		step::stop();
		step::start('cancel');
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		callback_query::edit_message(\content\saloos_tg\sarshomarbot\commands\step_cancel::step1());
		return [];
	}

	public static function preview($_query = null, $_data_url = null)
	{
		step::stop();
		step::start('create_preview');
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		callback_query::edit_message(\content\saloos_tg\sarshomarbot\commands\step_create_preview::step1());
		return [];
	}

	public static function advance($_query = null, $_data_url = null)
	{
		step::stop();
		step::start('create_advance');
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		callback_query::edit_message(\content\saloos_tg\sarshomarbot\commands\step_create_advance::step1());
		return [];
	}

	public static function save($_query = null, $_data_url = null)
	{
		step::stop();
		\content\saloos_tg\sarshomarbot\commands\step_cancel::step2(true);
		return [];
	}

	public static function delete($_query = null, $_data_url = null)
	{
		step::stop();
		\content\saloos_tg\sarshomarbot\commands\step_cancel::step2(false);
		return [];
	}
}
?>