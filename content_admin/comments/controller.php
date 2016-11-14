<?php
namespace content_admin\comments;

class controller extends \mvc\controller
{
	function _route()
	{
		$this->get("comments", "comments")->ALL("comments");
		$this->post("comments")->ALL("comments");
	}
}
?>