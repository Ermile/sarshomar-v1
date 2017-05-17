<?php
namespace content_election\admin;

class controller extends \content_election\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->access('election:admin:admin', 'block');
	}
}
?>