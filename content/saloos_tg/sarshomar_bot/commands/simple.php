<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class simple
{
	public static function exec($_cmd)
	{
		bot::$text = $_cmd['text'];
		bot::sendResponse();

		bot::$text = $_cmd['command'];
		bot::sendResponse();

		var_dump($_cmd);

		$response = null;
		var_dump($response);

		switch ($_cmd['command'])
		{
			case 'userid':
			case 'user_id':
			case 'myid':
				$response = self::userid();
				break;

			case 'تست':
			case 'test':
				$response = self::test();
				break;

			case 'بگو':
			case 'say':
				$response = self::say($_cmd);
				break;

			default:
				break;
		}

		return $response;
	}


	/**
	 * return userid
	 * @return [type] [description]
	 */
	public static function userid()
	{
		$result['text'] = 'Your userid: '. bot::response('from');
		return $result;
	}


	/**
	 * return sample test message
	 * @return [type] [description]
	 */
	public static function test()
	{
		$result['text'] = 'Test *'.ucfirst(core_name).'* bot on '. Domain;
		bot::$text = $result['text'];
		bot::sendResponse();
		return $result;
	}


	/**
	 * repeat given word!
	 * @param  [type]  $_text [description]
	 * @param  boolean $_full [description]
	 * @return [type]         [description]
	 */
	public static function say($_text, $_full = true)
	{
		$result['text'] = $_text;
		if(isset($_text['text']))
		{
			if(isset($_text['command']))
			{
				$len = strlen($_text['command']);
				$result['text'] = substr($_text['text'], $len +1);
			}
		}
		return $result;
	}
}
?>