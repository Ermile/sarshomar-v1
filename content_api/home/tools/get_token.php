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

		$authorization = utility::header("authorization") ? utility::header("authorization") : utility::header("Authorization");

		$token = null;
		if($_guest)
		{
			$token = \lib\utility\token::create_guest($authorization);
		}
		else
		{
			$token = \lib\utility\token::create_tmp_login($authorization);
		}
		return ['token' => $token];
	}

}
?>