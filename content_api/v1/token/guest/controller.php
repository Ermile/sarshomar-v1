<?php
namespace content_api\v1\token\guest;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("guest_token")->ALL("token/guest");
	}
}
?>