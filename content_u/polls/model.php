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
		$poll = \lib\db\polls::get_last($this->login("id"));
		if($poll['id'])
		{
			// save poll id into session to get in answer
			$_SESSION['last_poll_id']  = $poll['id'];
			$_SESSION['last_poll_opt'] = $poll['opt'];
			return $poll;
		}
		else
		{
			return null;
		}
	}


	/**
	 * save answers one poll
	 */
	function post_save_answer()
	{
		if(utility::post("poll_id"))
		{
			if(utility::post("poll_id") == $_SESSION['last_poll_id'])
			{
				$poll_id = $_SESSION['last_poll_id'];
			}
			else
			{
				\lib\debug::error(T_("poll id not match whit your last question"));
				return false;
			}
		}
		else
		{
			\lib\debug::error(T("poll id not found"));
			return false;
		}

		if(utility::post("type") == "bookmark")
		{
			$args =
			[
				'poll_id' => $poll_id,
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

			$answer_key  = utility::post("answer_key");
			$answer_text = utility::post("answer_text");

			if(isset($_SESSION['last_poll_opt'][$answer_key]))
			{
				\lib\db\answers::save($this->login('id'), $poll_id, $answer_key, $answer_text);
			}
			else
			{
				\lib\debug::error(T_("answer key not found"));
				return false;
			}
		}
	}
}
?>