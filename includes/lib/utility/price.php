<?php
namespace lib\utility;
use \lib\utility;
use \lib\debug;
use \lib\db;

/**
 * Class for price.
 */
class price
{

	use money;

	/**
	 * calc price of one poll or by filters
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function calc($_args)
	{
		if(!is_array($_args))
		{
			$_args = [$_args];
		}

		$default_args =
		[
			"id"       =>  null,
			"poll"     =>
			[
				"nofity" => null
			],
			"survey"   =>
			[
				"child" =>  null,
				"nofity" => null
			],
			"member"   =>  0,
			"branding" =>  null,
			"filters" =>
			[
				"gender"           =>  false,
				"marrital"         =>  false,
				"internetusage"    =>  false,
				"graduation"       =>  false,
				"degree"           =>  false,
				"range"            =>  false,
				"employmentstatus" =>  false,
				"housestatus"      =>  false
			]
		];

		$_args = array_merge($default_args, $_args);

		$price = 0;

		if((int) $_args['member'] == 0)
		{
			return $price;
		}

		$price += (self::$branding * (int) $_args['branding']);

		foreach ($_args['filters'] as $key => $value)
		{
			if(isset(self::$money_filter[$key]) && $value)
			{
				$price += $price * self::$money_filter[$key];
			}
		}
		return $price;
	}
}
?>