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
		$hook    = \lib\utility\social\tg::hook(true);
		$chat_id = self::tg_chat($hook);
		$data = ['chat_id' => $chat_id, 'text' => 'test 1'];
		var_dump($data);

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