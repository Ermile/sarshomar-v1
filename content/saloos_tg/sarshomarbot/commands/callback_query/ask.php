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

	public static function make($_query, $_data_url, $_options = [])
	{
		if(!is_array($_options))
		{
			$_options = [];
		}
		$options = array_merge([
			'poll_id' 	=> null,
			'type'		=> 'private',
			'last'		=> false,
			],$_options);


		$maker = new make_view($options['poll_id']);
		if(is_null($maker->query_result))
		{
			if($options['poll_id'] == null)
			{
				$text = T_("Hooray, You are answered to all of our polls.");
			}
			else
			{
				$text = T_("Poll not found!");
			}
			if(!$_query && !isset($options['return']))
			{
				bot::sendResponse(['text' => $text]);
			}
			else
			{
				return ['text' => $text];
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
		$multi_answer = session::get('expire', 'command', 'multi_answer', $maker->poll_id);
		if(isset($maker->query_result['options']['multi']) && $options['type'] == 'private')
		{
			if($multi_answer)
			{
				$my_answer = [];
				foreach ($multi_answer->answers as $key => $value) {
					$my_answer[] = ['key' => $value];
				}
			}
		}
		$maker->message->add_poll_list($my_answer);
		$maker->message->add_telegram_link();
		$maker->message->add_count_poll();
		if($options['type'] == 'inline')
		{
			if($maker->query_result['language'] == 'fa')
			{
				$date_now = new \DateTime("now", new \DateTimeZone('Asia/Tehran'));
				$my_date = \lib\utility::date('Y-m-d H:i:s', $date_now, 'current');
				$my_date = utility::nubmer_language($my_date);
			}
			else
			{
				$date_now = new \DateTime("now", new \DateTimeZone('Europe/London'));
				$my_date = \lib\utility::date('Y-m-d H:i:s', $date_now) . " GMT";
			}
			$maker->message->add('time',"🕰 " . $my_date);
		}

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
			$guest_option['share'] = false;
			$guest_option['update'] = false;
			$guest_option['report'] = false;
		}
		if(isset($maker->query_result['sarshomar']) && $maker->query_result['sarshomar'])
		{
			if($options['type'] == 'private')
			{
				$guest_option['share'] = true;
			}
			else
			{
				$guest_option['share'] = false;
			}
			if($options['type'] == 'private' && !$multi_answer && !empty($get_answer['available']))
			{
				$maker->message->message['poll_list'] .= "⏬ " . T_("Skip") ."\n";
			}
		}
		else
		{
			$guest_option['skip'] = false;
		}
		if(isset($maker->query_result['options']['multi']) && !empty($get_answer['available']))
		{
			$maker->message->message['poll_list'] .= $maker->query_result['options']['hint'] ."\n";
		}

		if($multi_answer)
		{
			$guest_option['share'] = false;
			$guest_option['update'] = false;
			$guest_option['report'] = false;
			$guest_option['inline_report'] = false;
			$guest_option['skip'] = false;
		}

		$maker->inline_keyboard->add_guest_option($guest_option);

		if($multi_answer)
		{
			$maker->inline_keyboard->add([
				[
					'text' => T_('Save'),
					'callback_data' => 'poll/answer/'. $maker->query_result['id'] . '/' .join('_', $multi_answer->answers) . '/+multi'
				],
				[
					'text' => T_('Cancel'),
					'callback_data' => 'ask/update/'.$maker->query_result['id']
				]
				]);
		}

		if($my_poll && $options['type'] == 'private')
		{
			$total_answer = $maker->query_result['result']['summary']['total'];
			if($total_answer &&
				(isset($maker->query_result['access_profile'])
				|| $maker->poll_type == 'descriptive')
			)
			{
				$maker->inline_keyboard->add([[
				'text' => T_('View results'),
				'callback_data' => 'poll/answer_results/'.$maker->query_result['id'],
				]]);
			}
			$maker->inline_keyboard->add_change_status();
		}

		if($options['type'] == 'private' && $options['last'] && !in_array('add', $get_answer['available']))
		{
			$maker->inline_keyboard->add([[
				'text' => T_('Next poll'),
				'callback_data' => 'ask/make',
				]]);
		}

		if(!$options['poll_id'] || $options['last'] || $options['type'] == 'inline')
		{
			foreach ($maker->inline_keyboard->inline_keyboard as $key => $value) {
				foreach ($value as $k => $v) {
					if(($options['last'] || !$options['poll_id']) && isset($v['callback_data']))
					{
						$maker->inline_keyboard->inline_keyboard[$key][$k]['callback_data'] .= '/last';
					}
					if($options['type'] == 'inline' &&
						isset($v['url']) && preg_match("/start=.*\d+$/", $v['url']))
					{
							$maker->inline_keyboard->inline_keyboard[$key][$k]['url'] .= '-subport_'.$options['inline_id'];
					}
				}
			}
		}

		if(isset($options['fn']))
		{
			$options['fn']($maker);
		}

		$return = $maker->make();
		if($options['type'] == 'private')
		{
			$return["response_callback"] = utility::response_expire('ask');
		}
		\lib\define::set_language(\lib\db\users::get_language((int) bot::$user_id), true);
		\lib\define::set_language($user_lang, true);

		$md5_result = md5(json_encode($maker->query_result['result']));
		// if(isset($options['md5_result']) && $md5_result == $options['md5_result'])
		// {
		// 	return false;
		// }
		// else
		if($_query || !isset($options['return']))
		{
			bot::sendResponse($return);
		}
		else
		{
			return $return;
		}
	}

	public static function update($_query, $_data_url)
	{
		session::remove('expire', 'command', 'multi_answer');
		\lib\storage::set_disable_edit(true);
		list($class, $method, $poll_id) = $_data_url;
		$mood = isset($_data_url[3]) ? $_data_url[3] : null;
		switch ($mood) {
			case 'update':
				$mood = 'update';
				break;

			default:
				$mood = $mood;
				break;
		}
		callback_query::edit_message(self::make(null, null, [
			'poll_id' 	=>$poll_id,
			'return' 	=> true,
			'last'		=> $mood == 'last'  ? true : false
			]));
		if($mood != 'update')
		{
			return ['text' => T_("Updated")];
		}
	}
}
?>