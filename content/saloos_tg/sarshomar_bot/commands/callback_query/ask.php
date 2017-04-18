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
			'text_type'	=> null,
			'flag'		=> [],
			],$_options);


		$maker = new make_view($options['poll_id']);
		if(is_null($maker->query_result) || !$maker->query_result)
		{
			if($options['poll_id'] == null)
			{
				$text = T_("Hooray, You are answered to all of our polls.");
				$text .= "\n" . T_("In the next days, check /ask again to answer more question.");
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
		if($maker->poll_type == 'emoji')
		{
			$maker->message->add_poll_list($my_answer);
			$maker->message->add('line', '');
			$maker->message->add_poll_chart();
		}
		else
		{
			$maker->message->add_poll_chart();
			$maker->message->add_poll_list($my_answer);
		}

		$maker->message->add_telegram_link();
		$maker->message->add_count_poll();



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
			$guest_option['report'] = false;
				$guest_option['inline_report'] = false;
			if($options['type'] == 'private')
			{
				$guest_option['share'] = true;
			}
			else
			{
				$guest_option['share'] = false;
				$guest_option['site_link'] = true;
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

		if(is_null($get_answer) || in_array('add', $get_answer['available']) || in_array('edit', $get_answer['available']))
		{
			$maker->inline_keyboard->add_poll_answers($options['type'] == 'private' ? $get_answer : null, $guest_option['skip']);
		}
		$guest_option['inline_report'] = false;

		$maker->inline_keyboard->add_guest_option($guest_option);
		// if(isset($maker->query_result['sarshomar']) && $maker->query_result['sarshomar'] && $options['type'] == 'inline')
		// {
		// $maker->inline_keyboard->add_guest_option($guest_option);

		// }
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
		if($options['type'] == 'inline')
		{
			if($maker->query_result['language'] == 'fa')
			{
				$date_now = new \DateTime("now", new \DateTimeZone('Asia/Tehran'));
				$my_date = \lib\utility::date('Y-m-d H:i:s', $date_now->getTimestamp(), 'current');
				$my_date = utility::nubmer_language($my_date);
			}
			else
			{
				$date_now = new \DateTime("now", new \DateTimeZone('Europe/London'));
				$my_date = \lib\utility::date('Y-m-d H:i:s', $date_now->getTimestamp()) . " GMT";
			}
			$maker->message->message['options'] .= " | 🕰 <code>" . str_replace("-", "/", $my_date) . "</code>";
		}

		if(in_array('gift', $options['flag']))
		{
			$maker->message->message['options'] = "🎁 با <a href='https://sarshomar.com/fa/enter'>ورود به سرشمار</a>، در روز پدر آیفون ببرید و ۱۰۰.۰۰۰ ریال هدیه سرشمار را دریافت کنید.\n" . $maker->message->message['options'];
		}
		if($options['type'] == 'private' && isset($maker->query_result['options']['prize']['value']) && isset($maker->query_result['options']['prize']['unit']))
		{
			$prize = utility::nubmer_language($maker->query_result['options']['prize']['value']);
			$maker->message->message['poll_list'] .= "💰 " . $prize . ' ' . T_($maker->query_result['options']['prize']['unit']) . "\n";
		}

		if($options['type'] == 'private' && isset($maker->query_result['my_prize']['value']) && isset($maker->query_result['my_prize']['unit']))
		{
			$prize = utility::nubmer_language($maker->query_result['my_prize']['value']);
			$maker->message->message['poll_list'] .= "💰💰 " . $prize . ' ' . T_($maker->query_result['my_prize']['unit']) . "\n";
		}
		$return = $maker->make();

		if(in_array('gift', $options['flag']) && $maker->query_result['sarshomar'])
		{
			if($maker->query_result['id'] == 'tZ9z')
			{
				$return['text'] = "<a href='https://dl.sarshomar.com/static/images/gift/iphone-football.jpg'>🎁</a> " . $return['text'];

			}
			elseif($maker->query_result['id'] == 'tZ9N')
			{
				$return['text'] = "<a href='https://dl.sarshomar.com/static/images/gift/iphone-cooking.jpg'>🎁</a> " . $return['text'];
			}
			elseif($maker->query_result['id'] == 'tZ9b')
			{
				$return['text'] = "<a href='https://dl.sarshomar.com/static/images/gift/iphone-food.jpg'>🎁</a> " . $return['text'];
			}
			elseif($maker->query_result['id'] == 'tZ9d')
			{
				$return['text'] = "<a href='https://dl.sarshomar.com/static/images/gift/iphone-raisi-vs-rouhani.png'>🎁</a> " . $return['text'];
			}
			else
			{
				$return['text'] = "<a href='https://sarshomar.com/static/images/gift/iphone.png'>🎁</a> " . $return['text'];
			}
			$return['disable_web_page_preview'] = false;
		}
		// handle::send_log($return);
		// exit();
		$file_support = ['gif', 'pdf', 'zip', 'mp3', 'mp4', 'jpg', 'jpeg', 'png'];
		$is_supported = false;
		if($maker->query_result['file'])
		{
			$exec = explode(".", $maker->query_result['file']['name']);
			$exec = end($exec);
			if(in_array($exec, $file_support))
			{
				$is_supported = true;
			}
		}
		$go_to_file = false;
		if($is_supported && $options['text_type'] != 'text' && isset($maker->query_result['file']))
		{
			if(($maker->query_result['file']['size']/1024/1024 < 5 && $options['type'] == 'inline'))
			{
				$go_to_file = true;
			}
			elseif($options['type'] == 'private')
			{
				$go_to_file = true;
			}
		}
		if($go_to_file)
		{

			if($maker->query_result['file']['type'] == 'archive')
			{
				$maker->query_result['file']['type'] = 'document';
			}
			$caption = $maker->query_result['title'];
			if($maker->message->message['descripttion'])
			{
				$caption .= "\n" . $maker->message->message['descripttion'];
			}
			if($maker->message->message['chart'])
			{
				$caption .= "\n" . $maker->message->message['chart'];
			}
			if($maker->message->message['poll_list'])
			{
				$caption .= "\n" . $maker->message->message['poll_list'];
			}
			$caption .= "\n👥". utility::nubmer_language($maker->message->stats['total']);
			$caption .= "\nt.me/sarshomar_bot?start=".$maker->query_result['id'];
			if(mb_strlen($caption) <= 150 || $options['text_type'] == 'caption')
			{
				if(isset($my_date))
				{
					$caption .= "\n🕰 " . str_replace("-", "/", $my_date);
				}
				$get_file = \lib\db\options::get([
					'option_cat' => 'telegram',
					'option_key' => 'file_uploaded_'.$maker->query_result['file']['id'],
					'limit'		=> 1
					]);
				$return['_url'] = $maker->query_result['file']['url'];
				if(!$get_file)
				{
					// var_dump($maker->query_result['file']);
					$filedata = str_replace("https://dl.sarshomar.com/", root . "public_html/", $maker->query_result['file']['url']);
					if($maker->query_result['file']['type'] == 'image')
					{
						$maker->query_result['file']['type'] = 'photo';
					}
					$filedata = curl_file_create($filedata, $maker->query_result['file']['mime'], $maker->query_result['file']['type']);
					if(substr($return['mime_type'], 0, 5) == 'video')
					{
						$maker->query_result['file']['type'] = 'video';
					}
					$return['method'] = "send" . $maker->query_result['file']['type'];
					unset($return['text']);
					$return['caption'] = $caption;
					$return[$maker->query_result['file']['type']] = $filedata;
					$return['is_json'] = false;
				}
				else
				{
					$get_file = $get_file['meta'];
					$unset = ['file_id', 'method', 'file_size'];
					foreach ($get_file as $key => $value) {
						if(in_array($key, $unset))
						{
							continue;
						}
						$return[$key] = $value;
					}
					unset($return['text']);
					$return['mime_type'] = $maker->query_result['file']['mime'];
					if(substr($return['mime_type'], 0, 5) == 'video')
					{
						$get_file['method'] = 'video';
					}
					if($get_file['method'] == 'image')
					{
						$get_file['method'] = 'photo';
					}
					$return['method'] = 'send' . $get_file['method'];
					$return[$get_file['method']] = $maker->query_result['file']['url'];
				}

				$return['caption'] = $caption;
				$return['_file_id'] = $maker->query_result['file']['id'];
			}
		}
		elseif(isset($maker->query_result['file']) && $maker->query_result['file']['size']/1024/1024 > 5)
		{
			$return["disable_web_page_preview"] = true;
		}

		\lib\define::set_language(\lib\utility\users::get_language((int) bot::$user_id), true);
		\lib\define::set_language($user_lang, true);

		if(isset($return['reply_markup']['inline_keyboard']))
		{
			foreach ($return['reply_markup']['inline_keyboard'] as $key => $value) {
				foreach ($value as $k => $v) {
					if(substr($v['callback_data'], 0, 20) == 'poll/answer_results/' && isset($return['caption']))
					{
						$addr = substr($v['callback_data'], 20);
						unset($return['reply_markup']['inline_keyboard'][$key][$k]['callback_data']);
						$return['reply_markup']['inline_keyboard'][$key][$k]['url'] = 'https://t.me/sarshomar_bot?start=' . $addr . '_answer_results';
					}
				}
			}
			handle::send_log($return['reply_markup']['inline_keyboard']);
		}

		if($options['type'] == 'private')
		{
			$return["response_callback"] = utility::response_expire('ask');
		}

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
			'last'		=> $mood == 'last'  ? true : false,
			'text_type'	=>  isset($_query['message']['text']) ? 'text' : 'caption'
			]));
		if($mood != 'update')
		{
			return ['text' => T_("Updated")];
		}
	}
}
?>