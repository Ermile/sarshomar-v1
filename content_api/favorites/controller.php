<?php
namespace content_api\favorites;

class controller extends  \mvc\controller
{	
	public function _route()
	{
		
		$this->get("getFavorites")->ALL("/getFavorites/");
		$this->post("getFavorites")->ALL("/getFavorites/");

		$this->post("addFavorites")->ALL("/addFavorites/");
	}
}
?>