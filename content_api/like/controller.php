<?php
namespace content_api\like;

class controller extends  \mvc\controller
{	
	public function _route()
	{
		$this->post("like")->ALL("addLike");
	}
}
?>