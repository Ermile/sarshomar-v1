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
			"id"               => null,
			"nofity"           => false,
			"survey"           => 0,
			"member"           => 0,
			"branding"         => false,
			"gender"           => false,
			"marrital"         => false,
			"internetusage"    => false,
			"graduation"       => false,
			"degree"           => false,
			"range"            => false,
			"employmentstatus" => false,
			"housestatus"      => false

		];

		$_args = array_merge($default_args, $_args);

		//
		// $default_args =
		// [
		// 	"id"       =>  null,
		// 	"poll"     =>
		// 	[
		// 		"nofity" => null
		// 	],
		// 	"survey"   =>
		// 	[
		// 		"child" =>  null,
		// 		"nofity" => null
		// 	],
		// 	"member"   =>  0,
		// 	"branding" =>  null,
		// 	"filters" =>
		// 	[
		// 		"gender"          =>  false,
		// 		"marrital"         =>  false,
		// 		"internetusage"    =>  false,
		// 		"graduation"       =>  false,
		// 		"degree"          =>  false,
		// 		"range"          =>  false,
		// 		"employmentstatus" =>  false,
		// 		"housestatus"      =>  false
		// 	]
		// ];

		// $_args = array_merge($default_args, $_args);

		$price = 0;


		// get saved data of the poll
		if($_args['id'])
		{
			$id   = \lib\utility\shortURL::decode($_args['id']);
			$poll = \lib\db\polls::get_poll($id);
			if(!$poll)
			{
				return debug::error(T_("Poll not found"), 'id', 'arguments');
			}

			$options =
			[
				'get_filter'         => true,
				'get_opts'           => true,
				'get_public_result'  => false,
				'get_advance_result' => false,
			];

			$result = (new \content_api\v1\home\model)->ready_poll($poll, $options);

			if(isset($result['filters']['member']))
			{
				$_args['member'] = $result['filters']['member'];
				unset($result['filters']['member']);
			}

			if(isset($result['filters']))
			{
				$_args['filters'] = $result['filters'];
			}

			// var_dump($result, $_args);
			// exit();
		}


		if((int) $_args['member'] == 0)
		{
			return $price;
		}

		$price = (int) $_args['member'];

		if($_args['branding'])
		{
			$price += (self::$branding * 1);
		}

		foreach ($_args as $key => $value)
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