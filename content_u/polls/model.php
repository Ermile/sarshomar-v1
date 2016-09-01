<?php
namespace content_u\polls;
use \lib\utility;

class model extends \mvc\model
{
	function get_show(){
		return \lib\db\polls::get_last($this->login("id"));
	}

	function post_save_answer() {

		$answer_id   = utility::post('answer_id');
		$poll_id     = utility::post('poll_id');
		$answer_text = utility::post('answer_text');

		\lib\db\answers::save($this->login('id'), $poll_id, $answer_id, $answer_text);

	}

	function get_polls($o){


		$poll_id = \lib\router::get_url(1);


		var_dump($poll_id);
	}

}
?>