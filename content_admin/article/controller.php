<?php
namespace content_admin\article;

class controller extends \content_admin\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get("article", "article")->ALL();
		$this->post("article")->ALL();
	}
}

?>