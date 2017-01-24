<?php
namespace content_api\v1\token\temp;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("temp_token", false)->ALL("v1/token/temp");
	}
}
?>