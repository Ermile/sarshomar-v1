<?php
namespace content\saloos_tg\sarshomar_bot;
// use tg class
use \lib\utility\social\tg as bot;

class controller extends \lib\mvc\controller
{
	/**
	 * allow telegram to access to this location
	 * to send response to our server
	 * @return [type] [description]
	 */
	function _route()
	{
		$myhook = 'saloos_tg/sarshomar_bot/'.\lib\utility\option::get('telegram', 'meta', 'hookFolder');
		if($this->url('path') == $myhook)
		{
			$result = self::tg_handle();
			if(\lib\utility\option::get('telegram', 'meta', 'debug'))
			{
				var_dump($result);
			}
			exit();
		}
	}

	static function about()
	{

	}


	/**
	 * handle tg requests
	 * @return [type] [description]
	 */
	static function tg_handle()
	{
		// run hook and get it
		$hook        = bot::hook();
		// extract chat_id if not exist return false
		$chat_id     = bot::response('chat');
		// define variables
		$command     = bot::response('text');

		// commands\whoami::exec();

		$reply       = bot::response('message_id');
		$text        = null;
		$replyMarkup = null;
		if(!$chat_id)
		{
			if(DEBUG)
			{
				$chat_id = \lib\utility::get('id');
				if(!$command)
				{
					$command = \lib\utility::get('cmd');
				}
			}
			else
			{
				return 'chat id is not exist!';
			}
		}
		switch ($command)
		{
			case '/start':
				$text = 'Welcome to ' . Domain;
				break;

			case 'about':
				$text = '['.T_('Sarshomar').'](http://sarshomar.ir)'."\r\n";
				$text .= T_("Sarshomar start jumping")."\r\n";
				$text .= 'Created and developed by Saloos';
				break;

			case 'photo':
				break;

			case 'userid':
				$text = 'your userid: '. bot::response('from');
				break;

			case 'test':
				$text = 'testing ' . Domain;
				break;

			case 'khar':
				$text = 'خر خودتی'."\r\n";
				$text .= 'باباته'."\r\n";
				$text .= 'بی تربیت'."\r\n";
				break;


			case 'cb_go_right':
				$text = 'رفتم راست'."\r\n";
				break;

			case 'cb_go_left':
				$text = 'رفتم چپ'."\r\n";
				break;


			case 'loc':
				$text = 'موثعیت تست'."\r\n";
				$replyMarkup =
				[
					'keyboard' =>
					[
						[
							[
								'text' => 'تقاضای شماره تلفن',
								'request_contact' => true
							],
							[
								'text' => 'تقاضای آدرس',
								'request_location' => true
							]
						]
					]
				];
				break;


			case 'menu':
				$text = 'منو'."\r\n";
				$replyMarkup =
				[
					'keyboard' =>
					[
							["شرکت در نظر سنجی"],
							["نظرسنجی های من"],
							["مقالات"],
							["پروفایل"],
					],
				];
				break;

			case 'inline':
				$text = 'کیبورد آزمایشی'."\r\n";
				// create keyboard
				$replyMarkup =
				[
					'inline_keyboard' =>
					[
						[
							[
								'text' => '<',
								'callback_data' => 'go_left'
							],
							[
								'text' => '^',
								'callback_data' => 'go_up'
							],
							[
								'text' => '>',
								'callback_data' => 'go_right'
							],
						],
						[
							[
								'text' => 'open google.com',
								'url' => 'google.com'
							],
							[
								'text' => 'search \'test\' inline',
								'switch_inline_query' => 'test'
							],
						]


							// ["مقالات"],
							// ["پروفایل"],
					],
				];
				break;

			default:
				$text = 'تعریف نشده';
				break;
		}


		if($chat_id && $text)
		{
			// generate data
			$data =
			[
				'chat_id'      => $chat_id,
				'text'         => $text,
				'parse_mode'   => 'markdown',
			];
			if($replyMarkup)
			{
				$data['reply_markup'] = json_encode($replyMarkup);
				$data['force_reply'] = true;
			}
			if($reply)
			{
				$data['reply_to_message_id'] = $reply;
			}

			if($command === 'cb_go_right' || $command === 'cb_go_left')
			{
				unset($data['chat_id']);
				$data['inline_message_id'] = $hook['callback_query']['id'];
				$result = bot::editMessageText($data);
			}
			else
			{
				$result = bot::sendMessage($data);
			}
			return $result;
		}

		// $result = \lib\utility\social\tg::getMe();
		return null;
	}
}
?>