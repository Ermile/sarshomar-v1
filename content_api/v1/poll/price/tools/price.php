<?php
namespace content_api\v1\poll\price\tools;
use \lib\utility;
use \lib\debug;

trait price
{

	public function poll_price($_args = null)
	{
		if(!utility::request())
		{
			return debug::error(T_("No parameter has been sent"), false, 'arguments');
		}

		if(utility::request("id") && count(utility::request()) > 1)
		{
			return debug::error(T_("You can not send id parameter and other parameters at the same time"), 'id', 'arguments');
		}

		$price = utility\price::calc(utility::request());

		return $price;
	}
}
?>