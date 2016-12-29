<?php
namespace content_u\add\model;
use \lib\utility;
use \lib\debug;

trait config
{


	/**
	 * Gets the poll identifier.
	 * check user permission and find the id
	 * the sarshomar personel  1 - 1 000 000
	 * and the other people +10 000 000
	 */
	function get_poll_id()
	{
		$sarshomar_id = 1000000; // 1 000 000
		if($this->access('u', 'sarshomar_knowledge', 'add'))
		{
			$next_id = (int) \lib\db\polls::sarshomar_id();
			$next_id++;

			if(intval($next_id) > $sarshomar_id)
			{
				\lib\debug::warn(T_("You are out of Sarshomar range ID"));
				return null;
			}
			elseif(intval($next_id) > $sarshomar_id / 2)
			{
				\lib\debug::warn(T_("You have used more than half of Sarshomar range ID"));
			}
			return $next_id;
		}
		else
		{
			// by table AUTO_INCREMENT set
			return null;
		}
	}
}
?>