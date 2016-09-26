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

		// list of query
		$this->get("question", "question")->ALL("question");

		// add new question
		$this->get(false, "question_add")->ALL("question/add");
		$this->post("question_add")->ALL("question/add");

		// edit question
		$this->get("question_edit", "question_edit")->ALL("/^question\/edit\=(\d+)$/");
		$this->post("question_edit")->ALL("/^question\/edit\=(\d+)$/");

		// delete question
		$this->get("question_delete", false)->ALL("/^question\/delete\/(\d+)$/");


		// add new question
		$this->get("question_filter", "question_filter")->ALL("/^question\/filter\=(\d+)$/");
		$this->post("question_filter")->ALL("/^question\/filter\/poll\=(\d+)$/");

	}
}
?>