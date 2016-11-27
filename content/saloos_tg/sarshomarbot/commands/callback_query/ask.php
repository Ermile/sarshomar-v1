<?php
namespace content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\callback_query;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use content\saloos_tg\sarshomarbot\commands\make_view;
use \content\saloos_tg\sarshomarbot\commands\utility;

class ask
{
	public static function start($_query, $_data_url)
	{
		if(count($_data_url) > 1)
		{
			$method = $_data_url[1];
			return self::$method($_query, $_data_url);
		}
		return [];
	}

	public static function make($_query, $_data_url, $_short_link = null)
	{
		$maker = new make_view(bot::$user_id, $_short_link);
		if(
			(
				$maker->query_result['status'] != 'publish' &&
				$maker->query_result['user_id'] != bot::$user_id
			) ||
			$maker->query_result['status'] == 'deleted'
		)
		{
			$return = ['text' => T_("Answer not found")];
		}
		else
		{
			$maker->message->add_title();
			$poll_access = \lib\utility\answers::check(bot::$user_id, $maker->poll_id);
			handle::send_log($poll_access);
			handle::send_log($maker->poll_id);
			$access = $poll_access->is_ok();
			$set_last = [];
			if(is_null($_short_link))
			{
				$set_last = ['callback_data' => function($_data){
					return $_data .'/last';
				}];
			}
			$skip = false;
			if($maker->query_result['status'] == 'publish' && $access)
			{
				$maker->inline_keyboard->add_poll_answers($set_last);
				$skip = true;
			}

			if($maker->query_result['user_id'] == bot::$user_id)
			{
				$maker->inline_keyboard->add_guest_option(['skip'=> false, 'poll_option' => true]);
				$maker->message->add_poll_chart(true);
				$maker->message->add_poll_list(true);
			}
			else
			{
				$maker->inline_keyboard->add_guest_option(['skip'=> $skip, 'update' => false, 'inline_report' => true]);
				$maker->message->add_poll_list(true, false);
			}
			$maker->message->add_telegram_link();
			$maker->message->add_telegram_tag();

			$return = $maker->make();

			$on_expire = $maker->inline_keyboard->get_guest_option(['skip' => false, 'poll_option' => true]);
			if($maker->query_result['status'] == 'publish')
			{
				$return["response_callback"] = utility::response_expire('ask', [
					'reply_markup' => [
						'inline_keyboard' => [$on_expire]
					]
				]);
			}
		}
		if(is_array($_query))
		{
			bot::sendResponse($return);
			return [];
		}
		return $return;
	}


	public static function change($_query, $_data_url){
		callback_query::edit_message(self::make(null, null, $_data_url[2]));
		session::remove_back('expire', 'inline_cache', 'ask');
		return [];
	}

	public static function poll($_query, $_data_url)
	{
		$poll_short_link = $_data_url[2];
		$answer_id = $_data_url[3];
		$poll_id = \lib\utility\shortURL::decode($poll_short_link);
		$save = \lib\utility\answers::save(bot::$user_id, $poll_id, $answer_id);
		$return_text = "❌ ";
		if($save->is_ok())
		{
			$answer_id = (int) $save->get_opt(0);
			$return_text = "✅ ";
		}

		$return_text .= $save->get_message();

		if($save->is_error_code(3000) || $save->is_error_code(3001))
		{
			// $return = self::make(null, null, $poll_short_link);
			callback_query::edit_message(['text' => $save->get_message()]);
		}
		elseif(!array_key_exists('message', $_query))
		{
			session::remove_back('expire', 'inline_cache');

			$maker = new make_view(bot::$user_id, $poll_short_link);
			$maker->message->add_title();
			$maker->message->add_poll_chart();
			$maker->message->add_poll_list();
			$maker->message->add_telegram_link();
			$maker->message->add_telegram_tag();

			$maker->inline_keyboard->add_poll_answers();
			$maker->inline_keyboard->add_guest_option(['skip' => false, 'update' => false, 'report' => true]);

			$inline_keyboard = $maker->inline_keyboard->make();

			if(!empty($inline_keyboard)) {
				$reply_markup = ['inline_keyboard' => $inline_keyboard];
			}
			callback_query::edit_message(['text' => $maker->message->make(), 'reply_markup' => $reply_markup]);
		}
		else
		{
			$on_edit = session::get_back('expire', 'inline_cache', 'ask', 'on_expire');

			$maker = new make_view(bot::$user_id, $poll_short_link);

			$maker->message->add_title();
			$maker->message->add_poll_chart($answer_id);
			$maker->message->add_poll_list($answer_id);
			$maker->message->add_telegram_link();
			$maker->message->add_telegram_tag();

			$maker->inline_keyboard->add_guest_option(['skip' => false, 'poll_option' => true]);

			$on_edit->text 				= $maker->message->make();
			$on_expire_keyboard = $maker->inline_keyboard->make();

			$on_edit->response_callback	= utility::response_expire('ask', ["reply_markup"=> ['inline_keyboard' => $on_expire_keyboard]]);
			if($save->is_ok())
			{
				array_unshift(
						$on_expire_keyboard[0],
						utility::inline(T_("Change poll"), "ask/change/". $poll_short_link)
					);
			}
			if(count($_data_url) > 4)
			{
				array_unshift(
					$on_expire_keyboard[0],
					utility::inline(T_("Next poll"), "ask/make")
				);
				foreach ($on_expire_keyboard[0] as $key => $value) {
					if(isset($on_expire_keyboard[0][$key]['callback_data']))
					{
						$on_expire_keyboard[0][$key]['callback_data'] = $value['callback_data'] . '/last';
					}
				}
			}
			$on_edit->reply_markup 		= ['inline_keyboard' => $on_expire_keyboard];
		}
		return ["text" => $return_text];
	}

	public static function update($_query, $_data_url)
	{
		\lib\storage::set_disable_edit(true);

		$maker = new make_view(bot::$user_id, $_data_url[2]);
		$maker->message->add_title();
		$maker->message->add_poll_chart(true);
		$maker->message->add_poll_list(true);
		$maker->message->add_telegram_link();
		$maker->message->add_telegram_tag();
		$update_time = date("H:i:s", $_query['message']['edit_date']);
		$maker->message->add('date', "_Last update: " . $update_time ."_");

		$maker->inline_keyboard->add_guest_option(['skip' => false, 'poll_option' => true]);

		$ask_expire = session::get('expire', 'inline_cache', 'ask', 'on_expire');
		if($ask_expire->message_id == $_query['message']['message_id'] AND
			$ask_expire->chat_id == $_query['message']['chat']['id'])
		{
			session::remove_back('expire', 'inline_cache', 'ask');
			if(end($_data_url) == 'last')
			{
				array_unshift(
					$maker->inline_keyboard->inline_keyboard[0],
					utility::inline(T_("Next poll"), "ask/make")
				);
				foreach ($maker->inline_keyboard->inline_keyboard[0] as $key => $value) {
					if(isset($maker->inline_keyboard->inline_keyboard[0][$key]['callback_data']))
					{
					$maker->inline_keyboard->inline_keyboard[0][$key]['callback_data'] = $value['callback_data'] . '/last';
					}
				}
			}
		}

 		callback_query::edit_message($maker->make());
		return [];
	}
}
?>