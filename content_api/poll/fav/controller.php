<?php
namespace content_api\poll\fav;

class controller extends  \content_api\home\controller
{
	public function _route()
	{
		$this->post("favorites")->ALL("poll/fav");
		$this->delete("favorites")->ALL("poll/fav");
	}
}
?>