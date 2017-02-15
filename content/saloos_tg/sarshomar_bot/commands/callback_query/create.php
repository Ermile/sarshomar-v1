<?php
namespace content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use content\saloos_tg\sarshomar_bot\commands\make_view;
use \lib\telegram\step;

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

	public function home($_query, $_data_url){
		$txt_text = T_("To upload your questions, enter the title of your question on the first line and its other options on the next lines. Notice that a valid question must contain at least one title and two answers.");
		$result   =
		[
			'text'         => $txt_text ."\n#create",
			"response_callback" => utility::response_expire('create'),
			'reply_markup' => [
				"inline_keyboard" => [
					[
						[
							"text" => T_("Cancel"),
							"callback_data" => 'create/cancel'
						],
						[
							"text" => T_("Options"),
							"callback_data" => 'create/options'
						]
					]
				]
			]
		];
		return $result;
	}

	public static function options($_query, $_data_url)
	{
		\lib\storage::set_disable_edit(true);
		$text = T_("Select your answers type");
		$text .= "\n";
		$text .= T_("Selective is default.");
		$result   =
		[
			'text'         => $text,
			"response_callback" => utility::response_expire('create'),
			'reply_markup' => [
				"inline_keyboard" => [
					[
						[
							"text" => T_("Selective"),
							"callback_data" => 'create/type/selective'
						],
						[
							"text" => T_("Emoji"),
							"callback_data" => 'create/type/emoji'
						]
					],
					[
						[
							"text" => T_("Like"),
							"callback_data" => 'create/type/like'
						],
						[
							"text" => T_("Descriptive"),
							"callback_data" => 'create/type/descriptive'
						]
					],
					[
						[
							"text" => T_("Cancel"),
							"callback_data" => 'create/cancel'
						]
					]
				]
			]
		];
		if(session::get('poll'))
		{
			handle::send_log(session::get('poll'));
			$inline_keyboard = $result['reply_markup']['inline_keyboard'];
			$result['reply_markup']['inline_keyboard'][count($inline_keyboard) -1][] = [
				"text" => T_("Back"),
				"callback_data" => 'create/back'
			];
		}
		callback_query::edit_message($result);
	}

	public static function cancel($_query, $_data_url)
	{
		session::remove_back('expire', 'inline_cache', 'create');
		session::remove('expire', 'inline_cache', 'create');
		step::stop();
		callback_query::edit_message(['text' => utility::tag(T_("Add poll canceled"))]);
		return [];
	}

	public static function close()
	{
		step::stop();
		session::remove('poll');
	}

	public static function back()
	{
		step::stop();
		session::remove('poll');
	}

	public static function type($_query, $_data_url)
	{
		session::set('poll_options' , 'type', $_data_url[2]);
	}
}
?>