<?php
namespace content\referer\token;
use \lib\debug;
use \lib\utility;
trait controller
{
	function route_token()
	{
		$token = utility::post('token') ? 'token:'.utility::post('token') : utility::get('to');
		if(!$this->check_for_login($token))
		{
			$this->route_check_true = true;
			return false;
		}
		$this->post('token')->ALL('referer/token');
		return true;
	}
}