<?php
namespace content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\callback_query;
use \content\saloos_tg\sarshomar_bot\commands\handle;
use \lib\db\tg_session as session;
use \lib\telegram\tg as bot;
use content\saloos_tg\sarshomar_bot\commands\make_view;
use \content\saloos_tg\sarshomar_bot\commands\utility;

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
		self::check_language($maker);
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

			$access = $poll_access->is_ok();
			$set_last = [];
			$skip_type = 'skip';
			if(is_null($_short_link))
			{
				$set_last = ['callback_data' => function($_data){
					return $_data .'/last';
				}];
				$skip_type = 'skip_last';
			}
			$skip = false;
			$update = true;
			if($maker->query_result['status'] == 'publish' && $access)
			{
				$maker->inline_keyboard->add_poll_answers($set_last);
				$skip = true;
				$update = false;
			}
			elseif(is_null($_short_link))
			{
				$maker->inline_keyboard->add([
					'text' => T_('Privacy violation'),
					'callback_data' => 'ask/make/last'
					]);
			}

			if($maker->query_result['user_id'] == bot::$user_id)
			{
				$maker->inline_keyboard->add_guest_option([$skip_type => false, 'poll_option' => true]);
				$maker->message->add_poll_chart(true);
				$maker->message->add_poll_list(true);
			}
			else
			{
				$maker->inline_keyboard->add_guest_option([$skip_type => $skip, 'update' => $update, 'inline_report' => true]);
				$maker->message->add_poll_chart(true);
				$maker->message->add_poll_list(true);
			}
			$maker->message->add_count_poll();
			$maker->message->add_telegram_link();
			$maker->message->add_telegram_tag();

			$return = $maker->make();

			$on_expire = $maker->inline_keyboard->get_guest_option([$skip_type => false, 'poll_option' => true]);
			if($maker->query_result['status'] == 'publish')
			{
				$disable_web_page_preview = true;
				if(isset($maker->query_result['meta']) && isset($maker->query_result['meta']['attachment_id']))
				{
					$disable_web_page_preview = false;
				}
				$return["response_callback"] = utility::response_expire('ask', [
					'reply_markup' => [
						'inline_keyboard' => [$on_expire]
					],
					'disable_web_page_preview' => $disable_web_page_preview

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

	public static function check_language($_maker)
	{
		if(!language::check())
		{
			language::set($_maker->query_result['language']);
		}
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
		$options = ['port' => 'telegram'];
		if(array_key_exists('inline_message_id', $_query))
		{
			$port = \lib\db\options::get([
				'limit' 		=> 1,
				'user_id' 		=> bot::$user_id,
				'post_id'		=> \lib\utility\shortURL::decode($poll_short_link),
				'option_cat'	=> 'telegram',
				'option_key'	=> 'subport',
				'option_value'	=> $_query['inline_message_id']
				]);
			if(!empty($port))
			{
				$options['subport'] = $port['id'];
			}
		}
		$poll_id = \lib\utility\shortURL::decode($poll_short_link);
		$save = \lib\utility\answers::save(bot::$user_id, $poll_id, $answer_id, $options);
		$return_text = "❌ ";
		if($save->is_ok())
		{
			$answer_id = (int) $save->get_opt(0);
			$return_text = "✅ ";
		}

		$return_text .= $save->get_message();

		if($save->is_error_code(3000) || $save->is_error_code(3001))
		{
			callback_query::edit_message(['text' => $save->get_message()]);
		}
		elseif(!array_key_exists('message', $_query))
		{
			session::remove_back('expire', 'inline_cache');

			$maker = new make_view(bot::$user_id, $poll_short_link);
			self::check_language($maker);
			\lib\define::set_language($maker->query_result['language'], true);
			$maker->message->add_title();
			$maker->message->add_poll_chart();
			$maker->message->add_poll_list();
			$maker->message->add_count_poll();
			$maker->message->add_telegram_link();
			$maker->message->add_telegram_tag();

			$maker->inline_keyboard->add_poll_answers();
			$maker->inline_keyboard->add_guest_option(['share'=> false, 'skip' => false, 'update' => false, 'report' => false]);

			$inline_keyboard = $maker->inline_keyboard->make();

			if(!empty($inline_keyboard)) {
				$reply_markup = ['inline_keyboard' => $inline_keyboard];
			}

			$disable_web_page_preview = true;
			if(isset($maker->query_result['meta']) && isset($maker->query_result['meta']['attachment_id']))
			{
				$disable_web_page_preview = false;
			}
			$inline_message_id = $_query['inline_message_id'];
			$lock = \lib\db\options::get([
				'option_cat' => 'telegram', 'option_key' => 'lock', 'option_value' => $inline_message_id, 'limit'=> 1
				]);
			if(empty($lock))
			{
				\lib\db\options::insert([
				'option_cat' => 'telegram', 'option_key' => 'lock', 'option_value' => $inline_message_id
				]);
				callback_query::edit_message([
					'text' => $maker->message->make(),
					'reply_markup' => $reply_markup,
					'disable_web_page_preview' => $disable_web_page_preview
					]);
				\lib\db\options::hard_delete([
				'option_cat' => 'telegram', 'option_key' => 'lock', 'option_value' => $inline_message_id
				]);
			}
			\lib\define::set_language(language::check(true), true);
		}
		else
		{
			$on_edit = session::get_back('expire', 'inline_cache', 'ask', 'on_expire');

			$maker = new make_view(bot::$user_id, $poll_short_link);

			$maker->message->add_title();
			$maker->message->add_poll_chart($answer_id);
			$maker->message->add_poll_list($answer_id);
			$maker->message->add_count_poll();
			$maker->message->add_telegram_link();
			$maker->message->add_telegram_tag();

			$maker->inline_keyboard->add_guest_option(['skip' => false, 'poll_option' => true, 'inline_report' => true]);

			$on_edit->text 				= $maker->message->make();
			$on_expire_keyboard = $maker->inline_keyboard->make();

			$disable_web_page_preview = true;
			if(isset($maker->query_result['meta']) && isset($maker->query_result['meta']['attachment_id']))
			{
				$on_edit->disable_web_page_preview = $disable_web_page_preview = false;
			}
			$on_edit->response_callback	= utility::response_expire('ask', [
				"reply_markup"=> ['inline_keyboard' => $on_expire_keyboard],
				'disable_web_page_preview' => $disable_web_page_preview
				]);

			if(count($_data_url) > 4)
			{
				array_unshift(
					$on_expire_keyboard,
					[utility::inline(T_("Next poll"), "ask/make")]
				);
			}
			foreach ($on_expire_keyboard as $key => $value) {
				foreach ($value as $k => $v) {
					if(array_key_exists('callback_data', $v))
					{
						$on_expire_keyboard[$key][$k]['callback_data'] .= '/last';
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
		$maker->message->add_count_poll();
		$maker->message->add_telegram_link();
		$maker->message->add_telegram_tag();

		$current_time = new \DateTime();
		$current_time->setTimezone(new \DateTimeZone('Europe/London'));
		$update_time = $current_time->format('Y-m-d H:i:s');

		$maker->message->add('date', utility::italic($update_time ." GMT"));
		$maker->inline_keyboard->add_guest_option(['skip' => false, 'poll_option' => true, 'inline_report' => true]);

		$ask_expire = session::get('expire', 'inline_cache', 'ask', 'on_expire');
		if($ask_expire->message_id == $_query['message']['message_id'] AND
			$ask_expire->chat_id == $_query['message']['chat']['id'])
		{
			session::remove_back('expire', 'inline_cache', 'ask');
			if(end($_data_url) == 'last')
			{
				array_unshift(
					$maker->inline_keyboard->inline_keyboard,
					[utility::inline(T_("Next poll"), "ask/make")]
				);
				foreach ($maker->inline_keyboard->inline_keyboard as $key => $value) {
					foreach ($value as $k => $v) {
						if(array_key_exists('callback_data', $v))
						{
							$maker->inline_keyboard->inline_keyboard[$key][$k]['callback_data'] .= '/last';
						}
					}
				}
			}
		}

 		callback_query::edit_message($maker->make());
		return [];
	}
}
?>