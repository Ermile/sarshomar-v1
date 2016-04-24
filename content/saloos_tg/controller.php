<?php
namespace content\saloos_tg;
class controller extends \lib\mvc\controller
{
	/**
	 * [_route description]
	 * @return [type] [description]
	 */
	function _route()
	{
		$myhook = 'saloos_tg/'.\lib\utility\option::get('telegram', 'meta', 'hook');
		if($this->url('path') == $myhook)
		{
			$result = self::tg_handle();
			if(DEBUG && false)
			{
				echo($result);
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
		$hook        = \lib\utility\social\tg::hook();
		// extract chat_id if not exist return false
		$chat_id     = self::tg_chat($hook);
		// define variables
		$command     = self::tg_text($hook);
		$reply       = self::tg_reply($hook);
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

			case 'test':
				$text = 'testing ' . Domain;
				break;

			case 'khar':
				$text = 'خر خودتی'."\r\n";
				$text .= 'باباته'."\r\n";
				$text .= 'بی تربیت'."\r\n";
				break;


			case 'menu':
				$text = 'منو'."\r\n";
				$replyMarkup =
				[
					'keyboard' =>
					[
						['text' => "شرکت در نظر سنجی 123"],
						['text' => "نظرسنجی های من"],
						['text' => "مقالات"],
						['text' => "پروفایل"],
					]
				];
				break;

			case 'inline':
				$text = 'کیبورد آزمایشی'."\r\n";
				// create keyboard
				$replyMarkup =
				[
				    'inline_keyboard' => array(
				        array("A", "B")
				    )
				];
				break;

			default:
				$text = 'صلوات';
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
			}
			if($reply)
			{
				$data['reply_to_message_id'] = $reply;
			}
			$result = \lib\utility\social\tg::sendMessage($data);
			return $result;
		}

		// $result = \lib\utility\social\tg::getMe();
		return null;
	}


	static function tg_from($_hook, $_needle = 'id')
	{
		if(isset($_hook['message']['from'][$_needle]))
		{
			return $_hook['message']['from'][$_needle];
		}
		return null;
	}


	static function tg_text($_hook)
	{
		if(isset($_hook['message']['from']))
		{
			if(isset($_hook['message']['text']))
			{
				$commad = $_hook['message']['text'];
				if(strpos($commad, 'خر') !== false)
				{
					$command = 'khar';
				}
				return $commad;
			}
		}
		return null;
	}


	static function tg_chat($_hook, $_needle = 'id')
	{
		if(isset($_hook['message']['chat'][$_needle]))
		{
			return $_hook['message']['chat'][$_needle];
		}
		return null;
	}


	static function tg_reply($_hook)
	{
		if(isset($_hook['message']['message_id']))
		{
			return $_hook['message']['message_id'];
		}
		return null;
	}
}
?>