<?php
namespace content\telegram;
use \lib\saloos;

class controller extends \lib\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		$this->post('tg', false)->ALL('telegram');
	}
}
?>