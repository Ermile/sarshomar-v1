<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;

class step_inline_query
{
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_text = null, $_skip = null)
	{
		return self::step1();
	}


	public static function step1()
	{
		$inline_query = bot::$hook['inline_query'];
		$id = $inline_query['id'];
		$result = ['method' => 'answerInlineQuery'];
		$result['inline_query_id'] = $id;
		// $result['next_offset'] = 'Go to next';

		$result['results'] = [];
		$result['results'][0]['type'] = 'article';
		$result['results'][0]['id'] = '1';
		$result['results'][0]['title'] = 'result 1';
		$result['results'][0]['input_message_content'] = ['message_text' => 'text message'];


		$result['results'][1]['type'] = 'article';
		$result['results'][1]['id'] = '2';
		$result['results'][1]['title'] = 'result: '. rand(100, 999);
		$result['results'][1]['input_message_content'] = [
		'message_text' => '*test*',
		'parse_mode' => 'Markdown',
		];
		$result['results'][1]['url'] = 'https://ermile.com';
		$result['results'][1]['description'] = 'poll number 1:)';


		$result['results'][2]['type'] = 'article';
		$result['results'][2]['id'] = '3';
		$result['results'][2]['title'] = 'result 2';
		$result['results'][2]['input_message_content'] = [
		'message_text' => "[test](http://www.ermile.com/)\ntime: ". date("H:i:s", time()) ."\ntext: " .$inline_query['query'],
		'parse_mode' => 'Markdown',
		];
		$result['results'][2]['url'] = 'https://ermile.com';
		$result['results'][2]['description'] = 'poll number 2 is true';
		$result['results'][2]['hide_url'] = true;
		$result['results'][2]['reply_markup'] = [
			"inline_keyboard" => [[
				[
					"text" => "answer one",
					"url" => "https://ermile.com"
				],
				[
					"text" => "answer two",
					// "url" => "https://telegram.me/sarshomarbot?start=poll_4-ans_2",
					// "switch_inline_query" => "hi987",
					"callback_data" => 'callbackData'
				]
			]]
		];
		return $result;
	}

	/**
	 * end define new question
	 * @return [type] [description]
	 */
	public static function stop($_cancel = false, $_text = null)
	{
		return $result;
	}

}
?>