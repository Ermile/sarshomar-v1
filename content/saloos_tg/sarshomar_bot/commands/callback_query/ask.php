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

	public static function make($_query, $_data_url, array $_options)
	{
		$options = array_merge([
			'poll_id' 	=> null,
			'type'		=> 'private'
			],$_options);

		if(!$options['poll_id'])
		{
			return ['text' => T_("In progress...")];
		}

		$maker = new make_view($options['poll_id']);
		if(is_null($maker->query_result))
		{
			if(!$_query && !isset($options['return']))
			{
				bot::sendResponse(['text' => T_("Poll not found")]);
			}
			else
			{
				return ['text' => T_("Poll not found")];
			}
			return ;
		}
		$user_lang = \lib\define::get_language();
		\lib\define::set_language($maker->query_result['language'], true);
		$my_poll = $maker->query_result['user_id'] == \lib\utility\shortURL::encode(bot::$user_id);

		$get_answer = null;
		$my_answer = null;
		if($options['type'] == 'private')
		{
			\lib\utility::$REQUEST = new \lib\utility\request([
				'method' 	=> 'array',
				'request' => [
					'id' 		=> $maker->query_result['id'],
				]
				]);

			$get_answer = \lib\main::$controller->model()->poll_answer_get([]);
			$my_answer = $get_answer['my_answer'];
		}
		$maker->message->add_title();
		$maker->message->add_poll_chart();
		$maker->message->add_poll_list($my_answer);
		$maker->message->add_telegram_link();
		$maker->message->add_count_poll();
		// $maker->message->add_telegram_tag();

		if(is_null($get_answer) || in_array('add', $get_answer['available']) || in_array('edit', $get_answer['available']))
		{
			$maker->inline_keyboard->add_poll_answers($options['type'] == 'private' ? $get_answer : null);
		}

		$guest_option = [];
		if(!$my_poll)
		{
			$guest_option = ['share' => false];
			if($options['type'] == 'private')
			{
				$guest_option['report'] = true;
				$guest_option['inline_report'] = true;
			}
			else
			{
				$guest_option['report'] = true;
			}
		}

		if($options['type'] == 'private' && $get_answer && in_array('add', $get_answer['available']))
		{
			$guest_option['skip'] = true;
		}
		else
		{
			$guest_option['skip'] =  false;
		}


		if($options['type'] != 'private'){
			\lib\storage::set_disable_edit(true);
			$guest_option['share'] = false;
			$guest_option['update'] = false;
			$guest_option['report'] = true;
		}


		$maker->inline_keyboard->add_guest_option($guest_option);

		if($my_poll && $options['type'] == 'private')
		{
			$maker->inline_keyboard->add_change_status();
		}

		if(!$options['poll_id'] || isset($options['last']))
		{
			foreach ($maker->inline_keyboard->inline_keyboard as $key => $value) {
				foreach ($value as $k => $v) {
					if(isset($v['callback_data']))
					{
						$maker->inline_keyboard->inline_keyboard[$key][$k]['callback_data'] .= '/last';
					}
				}
			}
		}

		$return = $maker->make();

		if($options['type'] == 'private')
		{
			$return["response_callback"] = utility::response_expire('ask');
		}
		\lib\define::set_language(\lib\db\users::get_language((int) bot::$user_id), true);
		\lib\define::set_language($user_lang, true);

		if(!$_query && !isset($options['return']))
		{
			bot::sendResponse($return);
		}
		else
		{
			return $return;
		}
	}

	public function update($_query, $_data_url)
	{
		list($class, $method, $poll_id) = $_data_url;
		\lib\storage::set_disable_edit(true);
		callback_query::edit_message(self::make(null, null, [
			'poll_id' 	=>$poll_id,
			'return' 	=> true,
			]));
		return ['text' => T_("Updated")];
	}
}
?>