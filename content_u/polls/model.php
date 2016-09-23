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
		if(utility::post("type") == "bookmark" && utility::post("poll_id"))
		{
			$args =
			[
				'poll_id' => utility::post("poll_id"),
				'user_id' => $this->login("id")
			];
			$result = \lib\db\polls::set_bookmark($args);
			if($result)
			{
				\lib\debug::true(T_("bookmark saved"));
			}
			else
			{
				\lib\debug::fatal(T_("error in save bookmark"));
			}
		}
		else
		{
			$answer_key   = utility::post('answer_key');
			$poll_id     = utility::post('poll_id');
			$answer_text = utility::post('answer_text');

			\lib\db\answers::save($this->login('id'), $poll_id, $answer_key, $answer_text);
		}
	}


	function get_polls($o)
	{

		$poll_id = \lib\router::get_url(1);
		var_dump($poll_id);
	}

}
?>