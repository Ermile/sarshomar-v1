<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \content\saloos_tg\sarshomar_bot\commands\utility;
use \content\saloos_tg\sarshomar_bot\commands\markdown_filter;
use \content\saloos_tg\sarshomar_bot\commands\make_view;
use \content\saloos_tg\sarshomar_bot\commands\menu;

class step_answer_descriptive
{
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_text = null)
	{
		step::stop();
		if(preg_match("/^([^_]*)_(.*)$/", $_text, $_answer))
		{


			\lib\utility::$REQUEST = new \lib\utility\request([
				'method' 	=> 'array',
				'request' => [
					'id' 		=> $_answer[1],
				]
				]);

			$get_answer = \lib\main::$controller->model()->poll_answer_get([]);
			$my_answer = $get_answer['my_answer'];

			$text = T_('Do you intend to answer the poll?');
			$maker = new make_view($_answer[1]);
			if($maker->poll_type == 'descriptive')
			{
				step::start('answer_descriptive');
				return self::step1($_answer[1], true);
			}
			elseif($maker->poll_type == 'like')
			{
				$answer_text = "❤️";
				$_answer[2] = 'like';
			}
			else
			{
				$answer_text = $maker::$emoji_number[$_answer[2]];
			}


			$maker->message->add_title();
			$maker->message->add_poll_list($my_answer, false);
			if(empty($get_answer['available']))
			{
				$maker->message->add('error', "❗️" . T_("You are not allowed to answer"));
				$maker->message->add_telegram_link();
				$maker->message->add_count_poll();
				$return = $maker->make();
				return $return;
			}
			$maker->message->add('insert_line', "");
			$maker->message->add('answer_text', T_("Your selected option is :answer_text", ['answer_text' => $answer_text]));
			if(isset($maker->query_result['access_profile']) && !is_null($maker->query_result['access_profile']))
			{
				$maker->message->add('access_profile', "\n⚠️ " . T_("By answering to this poll you allow Sarshomar to send your information to the questioner."));
			}
			$maker->message->add('tag', utility::tag(T_("Submit answer")));
			$maker->message->add_count_poll();
			$maker->message->add_telegram_link();
			$maker->inline_keyboard->add([
				[
					'text' => T_("Allow"),
					'callback_data' => 'poll/answer/' . $_answer[1] . '/' . $_answer[2]
				],
				[
					'text' => T_("Deny"),
					'callback_data' => 'poll/deny_answer/' . $_answer[1] . '/' . $_answer[2]
				]
			]);

			$return = $maker->make();

			$return["response_callback"] = utility::response_expire('answer_descriptive');

			return $return;
		}
		else
		{
			step::start('answer_descriptive');
			return self::step1($_text, true);
		}
	}
	public static function step1($_text = null, $check = false)
	{
		if(session::get('answer_descriptive', 'id'))
		{
			$poll_id = session::get('answer_descriptive', 'id');
		}
		else
		{
			$poll_id = $_text;
		}
		\lib\utility::$REQUEST = new \lib\utility\request([
			'method' 	=> 'array',
			'request' => [
				'id' 		=> $poll_id,
			]
		]);
		$get_answer = \lib\main::$controller->model()->poll_answer_get([]);
		if(array_search('add', $get_answer['available']) === false)
		{
			step::stop();
			return [
				'text' 						=> T_('You are not allowed to answer'),
				'reply_markup' 				=> menu::main(true),
				'parse_mode' 				=> 'HTML',
				'disable_web_page_preview' 	=> true
			];
		}
		elseif($check)
		{
			session::set('answer_descriptive', 'id', $_text);
			$maker = new make_view($poll_id);
			$maker->message->add_title();
			$maker->message->add_poll_list(null, false);
			$maker->message->add('insert_line', "");
			$maker->message->add('insert', T_('Please enter your answer'));
			if(isset($maker->query_result['access_profile']) && !is_null($maker->query_result['access_profile']))
			{
				$maker->message->add('access_profile', "\n⚠️ " . T_("By answering to this poll you allow Sarshomar to send your information to the questioner."));
			}
			$maker->message->add('tag', utility::tag(T_("Submit answer")));
			$maker->message->add_count_poll();
			$maker->message->add_telegram_link();
			$return = $maker->make();
			$return['reply_markup'] = ["remove_keyboard" => true];
			return $return;
		}
		else
		{
			session::set('answer_descriptive', 'text', $_text);
			$maker = new make_view($poll_id);
			$maker->message->add_title();
			$maker->message->message['title'] = '❔ ' . $maker->message->message['title'];
			$maker->message->add('answer' , '📝' . $_text);
			$maker->message->add('answer_line' , "");
			$maker->message->add('answer_verify' , '✅ ' . T_("Do you confirm the above answer?"));
			$maker->message->add('answer_change' , '✳️ ' . T_("If you intend to change the answer you can enter some other text"));
			$maker->message->add('tag' ,  utility::tag(T_("Submit answer")));
			$maker->message->add_count_poll();
			$maker->message->add_telegram_link();
			$maker->inline_keyboard->add([
				[
					'text' => T_('Yes'),
					'callback_data' => 'poll/answer_descriptive/answer',
				],
				[
					'text' => T_('Cancel'),
					'callback_data' => 'poll/answer_descriptive/cancel',
				]
			]);
			$return = $maker->make();

			$return["response_callback"] = utility::response_expire('answer_descriptive');
			return $return;
		}
	}
}
?>