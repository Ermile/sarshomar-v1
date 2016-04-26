<?php
namespace content\saloos_tg\sarshomar_bot;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class controller extends \lib\mvc\controller
{
	// use commands\simple;
	public static $text;
	public static $replyMarkup;

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
		self::$text = null;
		$text = '['.T_('Sarshomar').'](http://sarshomar.ir)'."\r\n";
		$text .= T_("Sarshomar start jumping")."\r\n";
		$text .= 'Created and developed by Saloos';
		self::$text = $text;
	}


	/**
	 * handle tg requests
	 * @return [type] [description]
	 */
	static function tg_handle()
	{
		// run hook and get it
		$hook        = bot::hook();
		// define variables
		// $command     = bot::response('text');
		$cmd         = bot::cmd();
		$command     = $cmd['command'];
		// extract chat_id if not exist return false
		$chat_id     = bot::response('chat');
		// reply to message id
		$reply       = bot::response('message_id');


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
				self::$text = 'Welcome to ' . Domain;
				break;

			case 'about':
				self::$text = '['.T_('Sarshomar').'](http://sarshomar.ir)'."\r\n";
				self::$text .= T_("Sarshomar start jumping")."\r\n";
				self::$text .= 'Created and developed by Saloos';
				break;

			case 'photo':
				break;

			case 'userid':
				commands\simple::userid();
				break;

			case 'test':
				commands\simple::test();
				break;

			case 'say':
			case 'بگو':
				commands\simple::say($cmd);
				break;


			case 'khar':
				self::$text = 'خر خودتی'."\r\n";
				self::$text .= 'باباته'."\r\n";
				self::$text .= 'بی تربیت'."\r\n";
				break;


			case 'cb_go_right':
				self::$text = 'رفتم راست'."\r\n";
				break;

			case 'cb_go_left':
				self::$text = 'رفتم چپ'."\r\n";
				break;


			case 'loc':
				self::$text = 'موثعیت تست'."\r\n";
				self::$replyMarkup =
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
				self::$text = 'منو'."\r\n";
				self::$replyMarkup =
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
				self::$text = 'کیبورد آزمایشی'."\r\n";
				// create keyboard
				self::$replyMarkup =
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
				self::$text = 'تعریف نشده';
				break;
		}


		if($chat_id && self::$text)
		{
			// generate data
			$data =
			[
				'chat_id'      => $chat_id,
				'text'         => self::$text,
				'parse_mode'   => 'markdown',
			];
			if(self::$replyMarkup)
			{
				$data['reply_markup'] = json_encode(self::$replyMarkup);
				$data['force_reply'] = true;
			}
			else
			{
				$data['reply_markup'] = null;
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