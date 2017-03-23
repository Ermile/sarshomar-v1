<?php
namespace content_admin\comments;

class controller extends \content_admin\main\controller
{
	function _route()
	{
		parent::_route();

		$this->get("comments", "comments")->ALL("comments");
		$this->post("comments")->ALL("comments");
	}
}
?>