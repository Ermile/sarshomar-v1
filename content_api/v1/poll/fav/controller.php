<?php
namespace content_api\v1\poll\fav;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->post("favorites")->ALL("poll/fav");
		$this->delete("favorites")->ALL("poll/fav");
	}
}
?>