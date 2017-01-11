<?php
namespace content_api\favorites;

class controller extends  \mvc\controller
{	
	public function _route()
	{	
		$this->post("favorites")->ALL("/fav/");
	}
}
?>