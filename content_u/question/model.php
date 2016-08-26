<?php
namespace content_u\question;

class model extends \mvc\model
{
	function get_question() {
		// in one page can be display 10 record of posts
		$page = 1;
		$lenght = 10;

		// set args to load query
		$_args =[
				'user_id'   => $this->login('id'),
				'post_type' => 'sarshomar',
				'page'     => $page,
				'lenght'       => $lenght
				];

		return \lib\db\polls::xget($_args);
	}

	function get_question_add() {

	}

	function get_question_edit() {

	}

	function get_question_delete() {

	}
}
?>