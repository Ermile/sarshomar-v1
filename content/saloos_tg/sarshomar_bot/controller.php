<?php
namespace content\saloos_tg\sarshomar_bot;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class controller extends \lib\mvc\controller
{
	// use commands\simple;
	public static $text;
	public static $replyMarkup;
	public static $action = 'sendMessage';
	public static $hook;
	public static $cmd;
	public static $chat_id;
	public static $message_id;

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

	/**
	 * handle tg requests
	 * @return [type] [description]
	 */
	static function tg_handle()
	{
		// run hook and get it
		$hook        = bot::hook();
		// define variables
		self::$cmd         = bot::cmd();
		// extract chat_id if not exist return false
		self::$chat_id     = bot::response('chat');
		// reply to message id
		self::$message_id       = bot::response('message_id');
		// call debug handler function
		self::debug_handler();


		// switch user commands
		switch (self::$cmd['command'])
		{
			// case '/start':
			// 	self::$text = 'Welcome to ' . Domain;
			// 	break;

			// case 'about':
			// 	self::$text = '['.T_('Sarshomar').'](http://sarshomar.ir)'."\r\n";
			// 	self::$text .= T_("Sarshomar start jumping")."\r\n";
			// 	self::$text .= 'Created and developed by Saloos';
			// 	break;

			// case 'photo':
			// 	break;

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
					],
				];
				break;

			default:
				self::default();
				break;
		}




		if(self::$chat_id && self::$text)
		{
			// generate data
			$data =
			[
				'chat_id'      => self::$chat_id,
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
			// save reply
			$data['reply_to_message_id'] = bot::response('message_id');

			if(self::$cmd['command'] === 'cb_go_right' || self::$cmd['command'] === 'cb_go_left')
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


	/**
	 * default action to handle message texts
	 * @param  [type] self::$cmd [description]
	 * @return [type]       [description]
	 */
	private static function default()
	{
		$response = null;
		// first run simple command if exist
		$response = commands\simple::exec(self::$cmd);
		if(!$response)
		{
			// then if not exist handel converstaion
			$response = commands\conversation::fa(self::$cmd);
		}
		if(!$response)
		{
			if(\lib\utility\option::get('telegram', 'meta', 'debug'))
			{
				// then if not exist set default text
				$response = ['text' => 'تعریف نشده'];
			}
		}

		// set text if exist
		if(isset($response['text']))
		{
			self::$text = $response['text'];
		}
		// set replyMarkup if exist
		if(isset($response['replyMarkup']))
		{
			self::$replyMarkup = $response['replyMarkup'];
		}
	}


	/**
	 * debug mode give data from user
	 * @return [type] [description]
	 */
	public static function debug_handler()
	{
		if(\lib\utility\option::get('telegram', 'meta', 'debug'))
		{
			if(!self::$chat_id)
			{
				self::$chat_id = \lib\utility::get('id');
				if(!self::$cmd['text'])
				{
					self::$cmd = bot::cmd(\lib\utility::get('text'));
				}
			}
		}
	}
}
?>