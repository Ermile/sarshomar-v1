<?php
namespace content_api\token\temp;

class controller extends  \content_api\home\controller
{
	public function _route()
	{
		$this->get("temp_token", false)->ALL("token/temp");
	}
}
?>