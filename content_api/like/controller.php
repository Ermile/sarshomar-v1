<?php
namespace content_api\like;

class controller extends  \mvc\controller
{	
	public function _route()
	{
		
		$this->get("getLike")->ALL("/getLike/");
		$this->post("getLike")->ALL("/getLike/");

		$this->post("addLike")->ALL("/addLike/");
	}
}
?>