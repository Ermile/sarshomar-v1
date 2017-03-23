<?php
namespace content_admin\article;

class controller extends \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get("article", "article")->ALL();
		$this->post("article")->ALL();
	}
}

?>