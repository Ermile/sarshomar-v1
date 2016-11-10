<?php
namespace content_u\home;

class model extends \mvc\model
{

	/**
	 * check short url and return the poll id
	 */
	public function check_poll_url($_args, $_type = "decode")
	{
		if(isset($_args->match->url[0]) && is_array($_args->match->url[0]))
		{
			if(!isset($_args->match->url[0][1]))
			{
				return false;
			}

			$url     = $_args->match->url[0][1];
			$poll_id = \lib\utility\shortURL::decode($url);

			// check is my poll this id
			if(!\lib\db\polls::is_my_poll($poll_id, $this->login('id')))
			{
				\lib\error::bad(T_("This not your poll"));
				return false;
			}

			if($_type == "decode")
			{
				return $poll_id;
			}
			else
			{
				return $url;
			}
		}
		else
		{
			// \lib\debug::error(T_("poll id not found"));
			return false;
		}
	}
}
?>