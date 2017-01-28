<?php
namespace content_api\v1\poll\price\tools;
use \lib\utility;
use \lib\debug;

trait price
{

	public function price($_args = null)
	{
		if(!utility::request())
		{
			return debug::error(T_("No parametr was send"), false, 'arguments');
		}

		if(utility::request("id") && count(utility::request()) > 1)
		{
			return debug::error(T_("Invalid use parametr id and other parametr"), 'id', 'arguments');
		}

		$price = utility\price::calc(utility::request());

		return $price;
	}
}
?>