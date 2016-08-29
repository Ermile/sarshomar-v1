<?php
namespace content_u\question;
use \lib\utility;

class model extends \mvc\model
{
	function get_question() {
		// in one page can be display 10 record of posts
		$page   = 1;
		$lenght = 10;

		$user_id = 1;
		$user_id = $this->login('id');
		// set args to load query
		$_args =[
				'user_id'   => $user_id,
				// 'post_type' =>  $user_id,
				// 'post_status' => "draft",
				'page'      => $page,
				'lenght'    => $lenght
				];

		return \lib\db\polls::xget($_args);
	}

	function post_question_add() {

		$args = [
				'user_id'     => $this->login('id'),
				'title'        => utility::post("title"),
				'type'         => 'private',
				'language'     => utility::post("language"),
				'content'      => utility::post("content"),
				'publish_date' => utility::post("publish_date"),
				'status'		=> 'draft',
				'answers' 	   => utility::post("answers")
				];


		$result  = \lib\db\polls::insert($args);


		if($result) {
			\lib\debug::true(T_("Add Question Success"));
		}else{
			\lib\debug::error(T_("Error in add question"));
		}

	}

	function get_question_edit() {

	}

	function get_question_delete() {

	}
}
?>