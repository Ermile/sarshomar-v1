<?php
namespace content_api\calcprice\tools;
use \lib\utility;
use \lib\debug;

trait price
{

	public function price($_args = null)
	{
		return utility\price::calc(utility::request());
	}
}
?>