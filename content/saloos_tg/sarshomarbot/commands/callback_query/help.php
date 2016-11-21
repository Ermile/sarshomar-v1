<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\menu;
use \content\saloos_tg\sarshomarbot\commands\utility;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;

class help
{
	public static function start($_query, $_data_url)
	{
		session::remove_back('expire', 'inline_cache', 'help');
		$method = self::find_method($_query, $_data_url);
		if($method)
		{
			callback_query::edit_message($method);
		}
		return [];
	}

	public static function find_method($_query, $_data_url){
		$method = $_data_url[1];
		$class_name = '\content\saloos_tg\sarshomarbot\commands\callback_query\help';
		if(class_exists($class_name) && method_exists($class_name, $_data_url[1]))
		{
			$call = self::{$_data_url[1]}($_query, $_data_url);
			$call['response_callback'] = utility::response_expire('help');
			return $call;
		}
		return false;
	}

	public static function home($_query, $_data_url)
	{
		return \content\saloos_tg\sarshomarbot\commands\step_help::start();
	}

	public static function faq($_query, $_data_url)
	{
		if(array_key_exists(2, $_data_url) && $_data_url[2] > 1)
		{
			if($_data_url[2] == 2)
			{
				return [
					'text' => "faq list 2/3\n4. Which devices can I use?\n5. Who are the people behind Telegram?\n6. Will you have ads? Or sell my data? Or steal my beloved and enslave my children?",
					"reply_markup"	=> [
						"inline_keyboard" => [
							[
								['text' => '◀️', 'callback_data' => 'help/faq/1'],
								['text' => 'help', 'callback_data' => 'help/home'],
								['text' => '▶️', 'callback_data' => 'help/faq/3'],
							]
						]
					]
				];
			}
			elseif($_data_url[2] == 3)
			{
				return [
					'text' => "faq list 3/3\n7. How are you going to make money out of this?\n8. What are your thoughts on internet privacy?\n9. There's illegal content on Telegram. How do I take it down?",
					"reply_markup"	=> [
						"inline_keyboard" => [
							[
								['text' => '◀️', 'callback_data' => 'help/faq/2'],
								['text' => 'help', 'callback_data' => 'help/home']
							]
						]
					]
				];
			}
		}
		else
		{
			return [
				'text' => "faq list 1/3\n1. Who\_ is Telegram\* for?\n2. How is Telegram different from WhatsApp?\n3. How old is Telegram?",
				"reply_markup"	=> [
					"inline_keyboard" => [
						[
							['text' => 'help', 'callback_data' => 'help/home'],
							['text' => '▶️', 'callback_data' => 'help/faq/2']
						]
					]
				]
			];
		}
	}
	public static function commands($_query, $_data_url)
	{
		return [
			'text' => "/help\n/commands\n/create\n/ask",
			"reply_markup"	=> [
				"inline_keyboard" => [
					[
						['text' => 'help', 'callback_data' => 'help/home'],
					]
				]
			]
		];
	}
}
?>