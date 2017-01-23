<?php
namespace content_api\poll\like;

class controller extends  \content_api\home\controller
{

	public function _route()
	{
		$this->post("like")->ALL("poll/like");
		$this->delete("like")->ALL("poll/like");
	}
}
?>