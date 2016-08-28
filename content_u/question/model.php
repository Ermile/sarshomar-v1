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

		$args = [
				'user_id'     => $this->login('id'),
				'title'        => utility::post("title"),
				'type'         => utility::post("type"),
				'language'     => utility::post("language"),
				'content'      => utility::post("content"),
				'publish_date' => utility::post("publish_date"),
				'answers' 	   => utility::post("answers")
				];


		$result  = \lib\db\polls::insert($args);

		var_dump($result);
		exit();
	}

	function get_question_edit() {

	}

	function get_question_delete() {

	}
}
?>