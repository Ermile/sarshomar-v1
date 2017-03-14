<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\telegram\tg as bot;
use \lib\telegram\step;
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \content\saloos_tg\sarshomarbot\commands\utility;
use \content\saloos_tg\sarshomarbot\commands\markdown_filter;
use \content\saloos_tg\sarshomarbot\commands\make_view;
use \content\saloos_tg\sarshomarbot\commands\menu;

class step_answer_descriptive
{
	/**
	 * create define menu that allow user to select
	 * @param  boolean $_onlyMenu [description]
	 * @return [type]             [description]
	 */
	public static function start($_text = null, $commands = [])
	{
		step::stop();
		session::remove('answer_descriptive');
		$subport = null;
		if(isset($commands['subport']))
		{
			$get_subport = \lib\db\options::get([
					'option_cat'	=> 'telegram',
					'option_key'	=> 'subport',
					'option_value'	=> $commands['subport'],
					'limit'			=> 1
					]);
			$subport = '/:' . \lib\utility\shortURL::encode($get_subport['id']);
		}
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
				session::set('answer_descriptive', 'subport', $subport);
				return self::step1($_answer[1], true);
			}
			elseif($maker->poll_type == 'like')
			{
				if(in_array('delete', $get_answer['available']))
				{
					$answer_text = "💔";
					$_answer[2] = 'dislike';
				}
				else
				{
					$answer_text = "❤️";
					$_answer[2] = 'like';
				}
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
					'callback_data' => 'poll/answer/' . $_answer[1] . '/' . $_answer[2] . $subport
				],
				[
					'text' => T_("Deny"),
					'callback_data' => 'poll/deny_answer/' . $_answer[1] . '/' . $_answer[2] . $subport
				]
			]);

			$return = $maker->make();

			$return["response_callback"] = utility::response_expire('answer_descriptive');
			return $return;
		}
		else
		{
			step::start('answer_descriptive');
			session::set('answer_descriptive', 'subport', $subport);
			return self::step1($_text, true);
		}
	}
	public static function step1($_text = null, $check = false)
	{
		$subport = session::get('answer_descriptive', 'subport');
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
		if(!is_array($get_answer) || empty($get_answer['available']))
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
			session::set('answer_descriptive', 'id', $poll_id);
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
					'callback_data' => 'poll/answer_descriptive/answer' . $subport
				],
				[
					'text' => T_('Cancel'),
					'callback_data' => 'poll/answer_descriptive/cancel' . $subport
				]
			]);
			$return = $maker->make();

			$return["response_callback"] = utility::response_expire('answer_descriptive');
			return $return;
		}
	}
}
?>