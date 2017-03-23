<?php
namespace content\enter;

class controller extends \content\main\controller
{
	/**
	 * check route of account
	 * @return [type] [description]
	 */
	function _route()
	{
		// parent::_route();

		$this->get('enter', 'enter')->ALL();
		$this->post('enter')->ALL();
	}
}
?>