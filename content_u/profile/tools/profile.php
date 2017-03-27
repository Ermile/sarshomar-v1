<?php
namespace content_u\profile\tools;
use \lib\utility;
use \lib\debug;

trait profile
{
	public function set_profile()
	{
		utility::set_request_array(utility::post());
		return $this->set_user_profile();
	}
}
?>