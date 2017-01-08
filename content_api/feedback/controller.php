<?php
namespace content_api\feedback;

class controller extends  \mvc\controller
{	
	public function _route()
	{
		$url = \lib\router::get_url(0);
	}
}
?>