<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class simple extends \content\saloos_tg\sarshomar_bot\controller
{
	public static function exec($_cmd)
	{
		$response = null;
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

			case 'say':
				$response = self::say($_cmd);
				break;

			default:
				break;
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
			$result['text'] = $_text['text'];
		}
		return $result;
	}
}
?>