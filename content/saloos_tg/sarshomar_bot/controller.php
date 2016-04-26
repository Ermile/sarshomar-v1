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
	public static $priority =
	[
		'callback',
		'menu',
		'user',
		'simple',
		'conversation',
	];

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
			$result = self::handle();
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
	static function handle()
	{
		// run hook and get it
		$hook          = bot::hook();
		// extract chat_id if not exist return false
		self::$chat_id = bot::response('chat');
		// define variables
		self::$cmd     = bot::cmd();
		// call debug handler function
		self::debug_handler();
		// generate response from defined commands
		self::generateResponse();

		// switch user commands
		switch (self::$cmd['command'])
		{
			case 'cb_go_right':
				self::$text = 'رفتم راست'."\r\n";
				break;

			case 'cb_go_left':
				self::$text = 'رفتم چپ'."\r\n";
				break;

			default:
				break;
		}



		if(self::$cmd['command'] === 'cb_go_right' || self::$cmd['command'] === 'cb_go_left')
		{
			unset($data['chat_id']);
			$data['inline_message_id'] = $hook['callback_query']['id'];
			$result = bot::editMessageText($data);
		}

		return self::sendResponse();
	}


	/**
	 * generate response and sending message
	 * @return [type] result of sending
	 */
	public static function sendResponse()
	{
		if(!self::$chat_id || !self::$text)
		{
			return false;
		}
		// generate data for response
		$data =
		[
			'chat_id'      => self::$chat_id,
			'text'         => self::$text,
			'parse_mode'   => 'markdown',
		];
		// create markup if exist
		if(self::$replyMarkup)
		{
			$data['reply_markup'] = json_encode(self::$replyMarkup);
			$data['force_reply'] = true;
		}
		else
		{
			$data['reply_markup'] = null;
		}
		// add reply message id
		$data['reply_to_message_id'] = bot::response('message_id');
		// call bot send message func
		$result = bot::sendMessage($data);
		// return result of sending
		return $result;
	}


	/**
	 * default action to handle message texts
	 * @param  [type] self::$cmd [description]
	 * @return [type]       [description]
	 */
	private static function generateResponse()
	{
		$response = null;
		foreach (self::$priority as $class)
		{
			// generate func name
			$funcName = __NAMESPACE__ .'\commands\\'.$class.'::exec';
			if(is_callable($funcName))
			{
				// get response
				$response = call_user_func($funcName, self::$cmd);
				// if has response break loop
				if($response)
				{
					break;
				}
			}
		}
		// if does not have response return default text
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