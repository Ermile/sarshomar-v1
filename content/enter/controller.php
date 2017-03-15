<?php
namespace content\enter;

class controller extends \mvc\controller
{
	/**
	 * check route of account
	 * @return [type] [description]
	 */
	function _route()
	{
		$this->get('enter', 'enter')->ALL();
		$this->post('enter')->ALL();
	}
}
?>