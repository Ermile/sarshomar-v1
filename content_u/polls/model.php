<?php
namespace content_u\polls;
use \lib\utility;

class model extends \mvc\model
{


	/**
	 * get last poll to answer users
	 *
	 * @return     <type>  The show.
	 */
	function get_show()
	{
		return \lib\db\polls::get_last($this->login("id"));
	}


	/**
	 * save answers one poll
	 */
	function post_save_answer()
	{

		$answer_key   = utility::post('answer_key');
		$poll_id     = utility::post('poll_id');
		$answer_text = utility::post('answer_text');

		\lib\db\answers::save($this->login('id'), $poll_id, $answer_key, $answer_text);
	}


	function get_polls($o)
	{

		$poll_id = \lib\router::get_url(1);
		var_dump($poll_id);
	}

}
?>