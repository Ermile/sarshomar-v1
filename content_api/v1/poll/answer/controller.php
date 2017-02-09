<?php
namespace content_api\v1\poll\answer;

class controller extends  \content_api\v1\home\controller
{
	public function _route()
	{
		$this->get("answer")->ALL("v1/poll/answer");

		$this->post("answer")->ALL("v1/poll/answer");

		$this->put("answer")->ALL("v1/poll/answer");

		$this->delete("answer")->ALL("v1/poll/answer");
	}
}
?>