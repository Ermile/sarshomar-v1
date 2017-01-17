<?php
namespace content_api\tag;

class controller extends  \mvc\controller
{	
	public function _route()
	{
		$this->get("tag", false)->ALL("tag");
		$this->post("tag")->ALL("tag");
	}
}
?>