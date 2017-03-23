<?php
namespace content_u\main;

class controller extends \mvc\controller
{
	function _route()
	{
		parent::_route();
		if(!$this->login())
		{
			$this->view()->data->notlogin = true;
		}
	}
}
?>