<?php
namespace content\saloos_tg;
class controller extends \lib\mvc\controller
{

	function _route()
	{
		$myhook = 'saloos_tg/'.\lib\utility\option::get('telegram', 'meta', 'hook');
		if($this->url('path') == $myhook)
		{
			echo('telegram');
			$result = \lib\utility\social\tg::hook();

			$this->_processor(['force_stop' => true, 'force_json' => false]);
		}
	}
}
?>