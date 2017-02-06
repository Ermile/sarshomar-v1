<?php
namespace content\referer\instagram;
use \lib\debug;
use \lib\utility;
trait controller
{
	function route_instagram()
	{
		// $token = utility::post('token') ? 'token:'.utility::post('token') : utility::get('to');
		// if(!$this->check_for_login($token))
		// {
		// 	$this->route_check_true = true;
		// 	return false;
		// }
		$this->post('instagram')->ALL('referer/instagram');
		$this->route('referer/instagram');

		return true;
	}
}