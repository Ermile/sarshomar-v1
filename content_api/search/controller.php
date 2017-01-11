<?php
namespace content_api\search;

class controller extends  \mvc\controller
{	
	public function _route()
	{
		$this->get("search", false)->ALL("search");
		$this->post("search")->ALL("search");
	}
}
?>