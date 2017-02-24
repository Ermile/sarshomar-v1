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
		step::start('answer_descriptive');
		return self::step1($_text, true);
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
				'text' 						=> T_('شما مجاز به ارسال پاسخ نیستید'),
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
			$maker->message->add('insert', T_('لطفا پاسخ خود را وارد کنید'));
			$maker->message->add('tag', utility::tag(T_("ارسال پاسخ")));
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
			$maker->message->add('answer_verify' , '✅ ' . T_("آیا پاسخ فوق را تایید می‌کنید؟"));
			$maker->message->add('answer_change' , '✳️ ' . T_("اگر قصد تغییر پاسخ دارید می‌توانید متن دیگری وارد کنید"));
			$maker->message->add('tag' ,  utility::tag(T_("ارسال پاسخ")));
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