<?php
namespace content\saloos_tg;
class controller extends \lib\mvc\controller
{

	function _route()
	{
		$myhook = 'saloos_tg/'.\lib\utility\option::get('telegram', 'meta', 'hook');
		if($this->url('path') == $myhook)
		{
			$result = self::tg_handle();
			if(DEBUG)
			{
				echo($result);
			}

			$this->_processor(['force_stop' => true, 'force_json' => false]);
		}
	}


	static function tg_handle()
	{
		// run hook and get it
		$hook    = \lib\utility\social\tg::hook();
		// extract chat_id if not exist return false
		$chat_id = self::tg_chat($hook);
		// define variables
		$text    = null;
		$chat_id = null;
		if(!$chat_id)
		{
			if(DEBUG)
			{
				$chat_id = \lib\utility::get('id');
			}
			else
			{
				return 'chat id is not exist!';
			}
		}
		switch (self::tg_text($hook))
		{
			case '/start':
				$text = 'Welcome to ' . Domain;
				break;

			case 'about':
				$text = '['.T_('Sarshomar').'](http://sarshomar.ir)'."\r\n";
				$text .= T_("Sarshomar start jumping")."\r\n";
				$text .= 'Created and developed by Saloos';
				break;

			case 'test':
				$text = 'testing ' . Domain;
				break;


			case 'testkeyboard':
				// create keyboard
				$keyboard =
				[
					'ReplyKeyboardMarkup' =>
					[
						'keyboard' =>
						[
							["A", "B"]
						]
					]
				];
				$keyboard = array('ReplyKeyboardMarkup' => array('keyboard' => array(array("A", "B"))));

				break;

			default:
				# code...
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
			if($keyboard)
			{
				$data['reply_markup'] = $keyboard;
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
				return $_hook['message']['text'];
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

}
?>