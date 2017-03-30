<?php
namespace content\saloos_tg\sarshomarbot\commands;
// use telegram class as bot
use \lib\db\tg_session as session;
use \content\saloos_tg\sarshomarbot\commands\handle;
use \lib\telegram\tg as bot;

class inline_query
{
	use inline_query\gift;

	public static function start($_query = null)
	{
		$inline_query = $_query;
		$id = $inline_query['id'];
		$result = ['method' => 'answerInlineQuery'];

		$result_id = md5(microtime(true) . $id);

		$result['inline_query_id'] = $id;
		$result['is_personal'] = true;
		$result['cache_time'] = 1;
		$result['switch_pm_text'] = T_("Create new poll");
		$result['switch_pm_parameter'] = "new";
		session::remove_back('expire', 'inline_cache');
		$search = \lib\utility\safe::safe($inline_query['query']);
		if($search == '/now' && in_array(bot::response('from'), [58164083, 46898544]))
		{
			self::now($result);
			return $result;
		}
		elseif(substr($search, 0, 6) == '/gift2')
		{
			self::gift2($result);
			return $result;
		}
		elseif(substr($search, 0, 5) == '/gift')
		{
			self::gift($result);
			return $result;
		}
		elseif(substr($search, 0, 1) == '/')
		{
			self::about($result);
			return $result;
		}
		$check_language = false;
		$explode = explode("#", $search, 2);
		$flag = [];
		if(isset($explode[1]))
		{
			$flag = str_replace("#", "", $explode[1]);
			$flag = explode(" ", $flag);
		}
		$explode[0] = trim($explode[0]);
		if(preg_match("/^\s*\\$(.*)$/", (string) $explode[0], $link_id))
		{
			\lib\utility::$REQUEST = new \lib\utility\request([
				'method' 	=> 'array',
				'request' => [
					'id' 		=> $link_id[1],
				]
				]);
			$query_result = \lib\main::$controller->model()->poll_get();
			$query_result = $query_result ? [$query_result] : [];
		}
		else
		{
			\lib\utility::$REQUEST = new \lib\utility\request([
				'method' 	=> 'array',
				'request' => [
					'search' 	=> $explode[0],
					'in' 		=> 'me sarshomar',
					'from'		=> !empty($_query['offset']) ? $_query['offset'] : 0
				]
				]);
			$query_result = \lib\main::$controller->model()->poll_search(true);
			if($query_result['from'] < $query_result['total'])
			{
				$result['next_offset'] = $query_result['to'];
			}
			$query_result = $query_result['data'];
		}


		$result['results'] = [];
		$step_shape = ['0⃣' , '1⃣', '2⃣', '3⃣', '4⃣', '5⃣', '6⃣', '7⃣', '8⃣', '9⃣' ];
		foreach ($query_result as $key => $value) {
			\lib\define::set_language($value['language'], true);
			$row_result = [];
			$row_result['type'] = 'article';
			if($value['sarshomar'] == true)
			{
				$row_result['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';
			}
			else
			{
				$row_result['thumb_url'] = 'http://sarshomar.com/static/images/telegram/sarshomar/sp-users.png';
			}
			$row_result['description'] = '';
			$poll = callback_query\ask::make(null, null, [
				'poll_id' 	=> $value['id'],
				'return'	=> true,
				'type'		=> 'inline',
				'inline_id'	=> $result_id,
				'flag'		=> $flag,
				'fn'		=> function($_maker) use(&$row_result)
				{
					$row_result['description'] = '👥 ' . utility::nubmer_language($_maker->query_result['result']['summary']['total']) .' ';
					if(isset($_maker->query_result['options']['multi']))
					{
						$row_result['not_sopport'] = true;
					}
					if(isset($_maker->query_result['file']))
					{
						$row_result['_url'] = $_maker->query_result['file']['url'];
					}
				}
				]);
			if(isset($row_result['not_sopport']))
			{
				continue;
			}
			$short_dec = preg_replace("/\n/", " ", $value['description']);
			$short_dec = mb_substr($short_dec, 0, 120);

			$row_result['description'] .= $short_dec;

			$row_result['title'] = $value['title'];

			if($value['sarshomar'])
			{
				$row_result['url'] = $value['short_url'];
			}
			$row_result['id'] = $result_id . ':' . $value['id'];
			if(in_array('gift', $flag))
			{
				$row_result['id'] .= ':#';
				$row_result['title'] = "🎁 " . $row_result['title'];
			}

			$row_result['hide_url'] = false;


			$row_result['reply_markup'] = $poll['reply_markup'];
			if(isset($poll['text']))
			{
				$row_result['input_message_content'] = [
					'message_text' 				=> $poll['text'],
					'parse_mode' 				=> $poll['parse_mode'],
					'disable_web_page_preview' 	=> $poll['disable_web_page_preview']
				];
			}
			elseif(isset($poll['caption']))
			{
				if($poll['method'] == 'senddocument')
				{
					if(substr($poll['mime_type'], 0, 6) == 'image/')
					{
						$poll['method'] = 'sendphoto';
						$poll['photo'] = $poll['document'];
						unset($poll['document']);
					}
				}
				$imethod = substr($poll['method'], 4);
				$unset = ['_file_id', 'reply_markup', 'disable_web_page_preview', 'parse_mode', 'method', $imethod];
				foreach ($poll as $key => $value) {
					if(in_array($key, $unset))
					{
						continue;
					}
					if($key == 'duration' && $imethod == 'audio')
					{
						$row_result['audio_duration'] = $value;
					}
					elseif($key == 'duration' && $imethod == 'video')
					{
						$row_result['video_duration'] = $value;
					}
					elseif($key == 'width' && $imethod == 'video')
					{
						$row_result['video_width'] = $value;
					}
					elseif($key == 'height' && $imethod == 'video')
					{
						$row_result['video_height'] = $value;
					}
					else
					{
						$row_result[$key] = $value;
					}
				}
				if($imethod == 'video' && !isset($row_result['mime_type']))
				{
					$row_result['mime_type'] = "video/mp4";
				}
				$row_result['type'] = $imethod;

				$row_result[$imethod . '_url'] = $row_result['_url'];
				if($imethod == 'photo')
				{
					$row_result['thumb_url'] = $row_result['_url'];
				}

				unset($row_result['_url']);
				foreach (['file_name', 'hide_url', 'thumb' , 'file_path'] as $key => $value) {
					if(isset($row_result[$value]))
					{
						unset($row_result[$value]);
					}
				}
			}
			var_dump($row_result);
			$result['results'][] = $row_result;
		}
		\lib\define::set_language(callback_query\language::check(true), true);

		return $result;
	}

	public static function about(&$result)
	{
		$result['results'][0] = [];
		$result['results'][0]['type'] = 'article';
		$result['results'][0]['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';
		$result['results'][0]['description'] = '';
		$result['results'][0]['title'] = "About / درباره";
		$result['results'][0]['url'] = "https://sarshomar.com";
		$result['results'][0]['id'] = "about_int";
		$text = "<strong>Sarshomar</strong>";
		$text .= "\n";
		$text .= "Ask Anyone Anywhere";
		$text .= "\n";
		$text .= "Focus on your question. Do not be too concerned about how to ask or analyze. Equipped with an integrated platform, Sarshomar has made it possible for you to ask your questions via any means.";
		$text .= "\n";
		$text .= "<a href='https://t.me/sarshomarbot?start=lang_en-ref_about'>Login to bot</a>";
		$text .= "\n";
		$text .= "<a href='https://sarshomar.com'>Sarshomar Website</a>";
		$text .= "\n";
		$text .= "\n";

		$text .= "<strong>سرشمار</strong>";
		$text .= "\n";
		$text .= "از هرکسی در هرجایی بپرسید";
		$text .= "\n";
		$text .= "روی سوال خود تمرکز کنید؛ دغدغه چطور پرسیدن و تحلیل کردن نداشته باشید. سرشمار با زیرساختی یکپارچه، امکان پرسیدن با هر ابزاری را برای شما فراهم کرده است.";
		$text .= "\n";
		$text .= "<a href='https://t.me/sarshomarbot?start=lang_fa-ref_about'>ورود به بات</a>";
		$text .= "\n";
		$text .= "<a href='https://sarshomar.com/fa'>وب‌سایت سرشمار</a>";
		$text .= "\n";
		$text .= "@SarshomarBot";
		$result['results'][0]['reply_markup']['inline_keyboard'] = [[
			[
				"text" 	=> "Bot",
				"url"	=> "https://t.me/sarshomarbot?start=lang_en-ref_about"
			],
			[
				"text" 	=> " Website",
				"url"	=> "https://sarshomar.com"
			]],
			[[
				"text" 	=> "ربات",
				"url"	=> "https://t.me/sarshomarbot?start=lang_fa-ref_about"
			],
			[
				"text" 	=> "وب‌سایت",
				"url"	=> "https://sarshomar.com/fa"
			]
		]];

		$result['results'][0]['input_message_content'] = [
				'message_text' 				=> $text,
				'parse_mode' 				=> "HTML",
				'disable_web_page_preview' 	=> true
			];

		// FA
		$result['results'][1] = [];
		$result['results'][1]['type'] = 'article';
		$result['results'][1]['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';
		$result['results'][1]['description'] = '';
		$result['results'][1]['title'] = "درباره";
		$result['results'][1]['url'] = "https://sarshomar.com/fa";
		$result['results'][1]['id'] = "about_fa";
		$text = "<strong>سرشمار</strong>";
		$text .= "\n";
		$text .= "از هرکسی در هرجایی بپرسید";
		$text .= "\n";
		$text .= "روی سوال خود تمرکز کنید؛ دغدغه چطور پرسیدن و تحلیل کردن نداشته باشید. سرشمار با زیرساختی یکپارچه، امکان پرسیدن با هر ابزاری را برای شما فراهم کرده است.";
		$text .= "\n";
		$text .= "<a href='https://t.me/sarshomarbot?start=lang_fa-ref_about'>ورود به بات</a>";
		$text .= "\n";
		$text .= "<a href='https://sarshomar.com/fa'>وب‌سایت سرشمار</a>";
		$text .= "\n";
		$text .= "@SarshomarBot";
		$result['results'][1]['reply_markup']['inline_keyboard'] = [[
			[
				"text" 	=> "ورود به بات",
				"url"	=> "https://t.me/sarshomarbot?start=lang_fa-ref_about"
			]],
			[[
				"text" 	=> "وب‌سایت سرشمار",
				"url"	=> "https://sarshomar.com/fa"
			],
		]];

		$result['results'][1]['input_message_content'] = [
				'message_text' 				=> $text,
				'parse_mode' 				=> "HTML",
				'disable_web_page_preview' 	=> true
			];

		// EN
		$result['results'][2] = [];
		$result['results'][2]['type'] = 'article';
		$result['results'][2]['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';
		$result['results'][2]['description'] = '';
		$result['results'][2]['title'] = "About";
		$result['results'][2]['url'] = "https://sarshomar.com";
		$result['results'][2]['id'] = "about_en";
		$text = "<strong>Sarshomar</strong>";
		$text .= "\n";
		$text .= "Ask Anyone Anywhere";
		$text .= "\n";
		$text .= "Focus on your question. Do not be too concerned about how to ask or analyze. Equipped with an integrated platform, Sarshomar has made it possible for you to ask your questions via any means.";
		$text .= "\n";
		$text .= "<a href='https://t.me/sarshomarbot?start=lang_en-ref_about'>Login to bot</a>";
		$text .= "\n";
		$text .= "<a href='https://sarshomar.com'>Sarshomar Website</a>";
		$text .= "\n";
		$text .= "@SarshomarBot";
		$result['results'][2]['reply_markup']['inline_keyboard'] = [[
			[
				"text" 	=> "Login to bot",
				"url"	=> "https://t.me/sarshomarbot?start=lang_en-ref_about"
			]],
			[[
				"text" 	=> "Sarshomar Website",
				"url"	=> "https://sarshomar.com"
			],
		]];

		$result['results'][2]['input_message_content'] = [
				'message_text' 				=> $text,
				'parse_mode' 				=> "HTML",
				'disable_web_page_preview' 	=> true
			];

		// Venue
		$result['results'][3] = [];
		$result['results'][3]['type'] = 'venue';
		$result['results'][3]['id'] = "venue";
		$result['results'][3]['latitude']  = 34.6314001;
		$result['results'][3]['longitude'] = 50.88625860000002;
		$result['results'][3]['title']     = T_('Contact');
		$result['results'][3]['address']   = T_('#614, Omranieh, Moallem Sq, Qom, Iran. 37158-39959');

		$result['results'][3]['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';

		$result['results'][3]['input_message_content'] = [
				'latitude'  => 34.6314001,
				'longitude' => 50.88625860000002,
				'title'     => T_('Sarshomar'),
				'address'   => T_('#614, Omranieh, Moallem Sq, Qom, Iran. 37158-39959')
			];
	}

	public static function now(&$result)
	{
		$result['results'][0] = [];
		$result['results'][0]['type'] = 'article';
		$result['results'][0]['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';
		$result['results'][0]['description'] = T_("Count of humans in Sarshomar until now");
		$result['results'][0]['title'] = ucfirst(T_("now"));
		$result['results'][0]['id'] = "now";

		$result['results'][0]['reply_markup']['inline_keyboard'] = [[
			[
				"text" 	=> T_("‌Bot"),
				"url"	=> "https://t.me/sarshomarbot?start=lang_fa-ref_about"
			],
			[
				"text" 	=> T_("Site"),
				"url"	=> "https://sarshomar.com/fa"
			],
		]];

		$result['results'][0]['input_message_content'] = [
				'message_text' 				=> utility::user_detail('now'),
				'parse_mode' 				=> "HTML",
				'disable_web_page_preview' 	=> true
			];


		$result['results'][1] = [];
		$result['results'][1]['type'] = 'article';
		$result['results'][1]['thumb_url'] = 'https://'.$_SERVER['SERVER_NAME'].'/static/images/logo/sarshomar-brand-128.png';
		$result['results'][1]['description'] = T_("Count of humans in Sarshomar until now");
		$result['results'][1]['title'] = ucfirst(str_replace("_", " ", T_("now_detail")));
		$result['results'][1]['id'] = "now_detail";

		$result['results'][1]['reply_markup']['inline_keyboard'] = [[
			[
				"text" 	=> T_("‌Bot"),
				"url"	=> "https://t.me/sarshomarbot?start=lang_fa-ref_about"
			],
			[
				"text" 	=> T_("Site"),
				"url"	=> "https://sarshomar.com/fa"
			],
		]];

		$result['results'][1]['input_message_content'] = [
				'message_text' 				=> utility::user_detail('now_detail'),
				'parse_mode' 				=> "HTML",
				'disable_web_page_preview' 	=> true
			];
	}
}
?>