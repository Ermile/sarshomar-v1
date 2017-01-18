<?php
namespace content_api\feedback;

trait controller
{
	public function route_feedback()
	{
		$url = \lib\router::get_url(0);
	}
}
?>