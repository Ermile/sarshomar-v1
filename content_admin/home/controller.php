<?php
namespace content_admin\home;

class controller extends \content_admin\main\controller
{

	/**
	 * rout
	 */
	function _route()
	{
		parent::_route();

		if(!$this->access('admin'))
		{
			\lib\error::access(T_("Access denied"));
		}
	}
}
?>