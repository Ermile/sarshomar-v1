<?php
namespace content_election\data;

class controller extends \content_election\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->access('election:data:admin', 'block');
	}
}
?>