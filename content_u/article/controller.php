<?php
namespace content_u\article;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get("article", "article")->ALL();
		$this->post("article")->ALL();
	}
}

?>