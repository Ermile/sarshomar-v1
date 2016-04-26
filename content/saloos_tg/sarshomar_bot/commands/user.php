<?php
namespace content\saloos_tg\sarshomar_bot\commands;
// use telegram class as bot
use \lib\utility\social\tg as bot;

class user extends \content\saloos_tg\sarshomar_bot\controller
{
	public static function exec($_cmd)
	{
		$response = null;
		switch ($_cmd['command'])
		{
			case '/start':
			case 'start':
			case 'شروع':
				$response = self::start();
				break;

			case '/about':
			case 'about':
			case 'درباره':
				$response = self::about();
				break;

			default:
				break;
		}

		return $response;
	}


	/**
	 * start conversation
	 * @return [type] [description]
	 */
	public static function start()
	{
		$result['text'] = 'Welcome to *Sarshomar*';
		return $result;
	}


	/**
	 * show about message
	 * @return [type] [description]
	 */
	public static function about()
	{
		$result['text'] = '['.T_('Sarshomar').'](http://sarshomar.ir)'."\r\n";
		$result['text'] .= T_("Sarshomar start jumping")."\r\n";
		$result['text'] .= 'Created and developed by '.ucfirst(core_name);
		return $result;
	}
}
?>