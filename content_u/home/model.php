<?php
namespace content_u\home;

class model extends \mvc\model
{

	/**
	 * get data for users profile
	 *
	 * @return     <type>  The profile.
	 */
	function get_profile()
	{
		return 'profile';
	}


	/**
	 * check short url and return the poll id
	 */
	public function check_poll_url($_args, $_type = "decode")
	{
		if(isset($_args->match->url[0]) && is_array($_args->match->url[0]))
		{
			$url = $_args->match->url[0][1];
			if($_type == "decode")
			{
				return \lib\utility\shortURL::decode($url);
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