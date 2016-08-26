<?php
namespace content_u\question;
use \lib\utility;

class model extends \mvc\model
{
	function get_question() {
		// in one page can be display 10 record of posts
		$page   = 1;
		$lenght = 10;

		// set args to load query
		$_args =[
				'user_id'   => $this->login('id'),
				'post_type' => 'sarshomar',
				'page'      => $page,
				'lenght'    => $lenght
				];

		return \lib\db\polls::xget($_args);
	}

	function post_question_add() {
		$title = utility::post("title");
		$answers = utility::post("answers");



		var_dump($answers);
		exit();
	}

	function get_question_edit() {

	}

	function get_question_delete() {

	}
}
?>