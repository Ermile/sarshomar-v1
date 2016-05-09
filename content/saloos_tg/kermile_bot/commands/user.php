<?php
namespace content\saloos_tg\kermile_bot\commands;
// use telegram class as bot
use \lib\utility\telegram\tg as bot;

class user
{
	/**
	 * execute user request and return best result
	 * @param  [type] $_cmd [description]
	 * @return [type]       [description]
	 */
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
	 * start
	 * @return [type] [description]
	 */
	public static function start()
	{
		// disable return from main menu
		$txt_text = "سلام، من ربات فست فود ` آزمایشی کرمایل ` هستم.\n چه کاری می خواهید انجام دهید؟";

		$menu =
		[
			'keyboard' =>
			[
				["ثبت سفارش"],
				["درباره ما", "مشاهده منو"],
			],
		];

		$result   =
		[
			[
				'text'         => $txt_text,
				'reply_markup' => $menu,
			],
		];

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