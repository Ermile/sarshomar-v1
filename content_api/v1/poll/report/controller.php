<?php
namespace content_api\v1\poll\report;

class controller extends  \content_api\v1\home\controller
{
	public function route_feedback()
	{
		$url = \lib\router::get_url(0);
	}
}
?>