<?php
namespace content_api\fav;

trait controller
{
	public function route_fav()
	{
		$this->post("favorites")->ALL("fav");
	}
}
?>