<?php
namespace content_api\token\guest;

class controller extends  \content_api\home\controller
{
	public function _route()
	{
		$this->get("guest_token")->ALL("token/guest");
	}
}
?>