<?php
namespace content_u\question;

class controller extends \content_u\home\controller
{
	function _route() {
		parent::check_login();
		// check logined
		if(!$this->login()){
			$this->redirector()->set_domain()->set_url('login')->redirect();
		}


		$this->get("question", "question")->ALL("question");
		$this->get("question_add", "question_add")->ALL("question/add");
		$this->get("question_edit", "question_edit")->ALL("/^question\/edit\/(\d+)$/");
		$this->get("question_delete", null)->ALL("/^question\/delete\/(\d+)$/");

	}
}
?>