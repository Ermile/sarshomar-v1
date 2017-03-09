<?php
namespace content_api\v1\poll\answers;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("poll_answers")->ALL("v1/poll/answers");
	}
}
?>