<?php
namespace content_api\v1\poll\fav;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->post("favorites")->ALL("v1/poll/fav");
		$this->delete("favorites")->ALL("v1/poll/fav");
	}
}
?>