<?php
namespace content_api\home;

class controller extends  \mvc\controller
{
	public function _route()
	{
		$url = \lib\router::get_url();
		if($url == '')
		{
			$this->redirector('api/v1')->redirect();
			return;
		}
	}
}
?>