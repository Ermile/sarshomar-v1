<?php
namespace content_api\like;

trait controller
{

	public function route_like()
	{
		$this->post("like")->ALL("like");
	}
}
?>