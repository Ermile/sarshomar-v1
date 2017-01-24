<?php
namespace content_api\v1\poll\like;

class controller extends  \content_api\v1\home\controller
{

	public function _route()
	{
		$this->post("like")->ALL("v1/poll/like");
		$this->delete("like")->ALL("v1/poll/like");
	}
}
?>