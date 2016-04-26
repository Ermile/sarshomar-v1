<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class callback
{
	/**
	 * execute user request and return best result
	 * @param  [type] $_cmd [description]
	 * @return [type]       [description]
	 */
	public static function exec($_cmd)
	{
		bot::$text = 'callback';
		bot::sendResponse();

		$response = null;
		switch ($_cmd['command'])
		{
			case 'cb_go_right':
				$response = self::go_right();
				break;

			case 'cb_go_left':
				$response = self::go_left();
				break;

			default:
				break;
		}
		if($response)
		{
			self::$callback = true;
		}

		bot::$text = 'callback okay'. $_cmd['command'];
		bot::sendResponse();

		return $response;
	}


	/**
	 *
	 * @return [type] [description]
	 */
	public static function go_right()
	{
		$result['text'] = 'رفتم راست';
		return $result;
	}


	/**
	 *
	 * @return [type] [description]
	 */
	public static function go_left()
	{
		$result['text'] = 'رفتم چپ';
		return $result;
	}
}
?>