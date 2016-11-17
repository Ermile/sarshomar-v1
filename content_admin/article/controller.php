<?php
namespace content_admin\article;

class controller extends \mvc\controller
{
	public function _route()
	{
		$this->get("article", "article")->ALL();
		$this->post("article")->ALL();
	}
}

?>