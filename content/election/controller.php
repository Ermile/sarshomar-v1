<?php
namespace content\election;
use \lib\saloos;

class controller extends \content\main\controller
{
	function _route()
	{
		parent::_route();

		$this->get('election', 'election')->ALL("/election/");
		$this->post('election')->ALL("/election/");
	}
}
?>