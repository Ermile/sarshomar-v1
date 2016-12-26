<?php
namespace content_admin\comments;

class controller extends \content_admin\home\controller
{
	function _route()
	{
		parent::check_login();

		$this->get("comments", "comments")->ALL("comments");
		$this->post("comments")->ALL("comments");
	}
}
?>