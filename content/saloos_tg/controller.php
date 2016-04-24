<?php
namespace content\saloos_tg;
class controller extends \lib\mvc\controller
{

	function _route()
	{
		$myhook = 'saloos_tg/'.\lib\utility\option::get('telegram', 'meta', 'hook');
		if($this->url('path') == $myhook)
		{
			self::tg_handle();
			$this->_processor(['force_stop' => true, 'force_json' => false]);
		}
	}


	static function tg_handle()
	{
		// run hook and get it
		$hook    = \lib\utility\social\tg::hook(true);
		// extract chat_id if not exist return false
		$chat_id = self::tg_chat($hook);
		if(!$chat_id)
		{
			return 'chat id is not exist!';
		}

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
		// generate data
		$data =
		[
			'chat_id'      => $chat_id,
			'text'         => 'test message send from sarshomar!',
			'reply_markup' => $keyboard
		];

		$result = \lib\utility\social\tg::sendMessage($data);
		var_dump($result);
	}


	static function tg_from($_hook, $_needle = 'id')
	{
		if(isset($_hook['message']['from'][$_needle]))
		{
			return $_hook['message']['from'][$_needle];
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