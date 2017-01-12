<?php
namespace content_api\fav_like;

class controller extends  \mvc\controller
{	
	public function _route()
	{
		$this->post("favorites")->ALL("fav");
		
		$this->post("like")->ALL("like");
	}
}
?>