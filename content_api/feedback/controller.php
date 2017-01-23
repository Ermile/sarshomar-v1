<?php
namespace content_api\feedback;

class controller extends  \content_api\home\controller
{
	public function route_feedback()
	{
		$url = \lib\router::get_url(0);
	}
}
?>