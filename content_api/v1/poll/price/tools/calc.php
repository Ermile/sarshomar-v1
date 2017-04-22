<?php
namespace content_api\v1\poll\price\tools;
use \lib\utility;
use \lib\debug;

trait calc
{

	public function poll_price_calc($_args = null)
	{
		if(!utility::request())
		{
			debug::error(T_("No parameter has been sent"), false, 'arguments');
			return false;
		}

		if(utility::request("id") && count(utility::request()) > 1)
		{
			debug::error(T_("You can not send id parameter and other parameters at the same time"), 'id', 'arguments');
			return false;
		}

		$price = utility\price::calc(utility::request());

		return $price;
	}
}
?>