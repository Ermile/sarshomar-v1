<?php
namespace content_api\home\tools;
use \lib\utility;
use \lib\debug;

trait get_token
{
	/**
	 * make token
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function token($_guest = false)
	{

		$guest_token = null;
		if(utility::request("guest"))
		{
			$guest_token = utility::request("guest");
		}

		$authorization = utility::header("authorization") ? utility::header("authorization") : utility::header("Authorization");

		$token = null;
		if($_guest)
		{
			$token = \lib\utility\token::create_guest($authorization);
		}
		else
		{
			$token = \lib\utility\token::create_tmp_login($authorization, $guest_token);
		}
		return ['token' => $token];
	}

}
?>