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

		$poll_id     = utility\shortURL::decode($_args['id']);
		$price       = 0;
		$price_array = [];

		if(true || !\content_api\v1\home\tools\api_options::check_api_permission('u', 'sarshomar', 'view'))
		{
			// $member  = (int) \lib\db\ranks::get($poll_id, 'member');
			// if($member > 0)
			// {
			// 	$price += $member * self::$member_per_person;
			// }
		}

		$filters = utility\postfilters::get_filter($poll_id);
		$filters = array_filter($filters);
		$percent = 0;

		if(!empty($filters))
		{
			if(true || !\content_api\v1\home\tools\api_options::check_api_permission('u', 'free_add_filter', 'view'))
			{
				foreach ($filters as $key => $value)
				{
					if(isset(self::$money_filter[$key]))
					{
						$percent += self::$money_filter[$key];
					}
				}
			}
		}

		if($price && $percent)
		{
			$price += ($percent * $price) / 100;
		}

		$have_brandin = \lib\db\options::get(
		[
			'user_id'		=> null,
			'post_id'       => $poll_id,
			'option_cat'    => 'poll_'. $poll_id,
			'option_key'    => 'brand',
			'option_status' => 'enable',
			'limit'         => 1,
		]);

		if(!empty($have_brandin))
		{
			if($price)
			{
				$price = ($price * self::$add_poll_brand) + $price;
			}
			else
			{
				$price += self::$add_poll_brand;
			}
		}

		$hide_result = \lib\db\options::get(
		[
			'user_id'		=> null,
			'post_id'       => $poll_id,
			'option_cat'    => 'poll_'. $poll_id,
			'option_key'    => 'hide_result',
			'option_status' => 'enable',
			'limit'         => 1,
		]);

		if(!empty($hide_result))
		{
			$price += self::$add_poll_hide_result;
		}

		if(!$price)
		{
			return true;
		}

		$price = 0;
	}


	/**
	 * minus money form user cash
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function run($_args)
	{
		if(\content_api\v1\home\tools\api_options::check_api_permission('u', 'free_account', 'view'))
		{
			// return true;
		}

		if(\content_api\v1\home\tools\api_options::check_api_permission('u', 'free_add_poll', 'view'))
		{
			// return true;
		}


		$user_budget = transactions::budget($_args['user_id']);
		$user_unit   = \lib\db\units::find_user_unit($_args['user_id'], true);
		if(!$user_unit)
		{
			debug::error(T_("Please go to billing page and set your unit to save poll transactions"));
			return false;
		}

		$price = \lib\db\exchangerates::change_unit_to('sarshomar', $user_unit, $price);

		if(array_sum($user_budget) < $price )
		{
			debug::error(T_("Low cash, Please charge your cash at billing page"), 'money', 'permission');
			return false;
		}

		$complete    = false;

		if(isset($user_budget['real']) && !$complete)
		{
			$real_poll = floatval($user_budget['real']);
			if(($real_poll - $price) >= 0)
			{
				$must_minus = $price;
				$complete = true;
			}
			else
			{
				$must_minus = $real_poll;
			}

			if($must_minus)
			{
				transactions::set("add:poll:minus:real", $_args['user_id'], ['minus' => $must_minus]);
				$price = $price - $real_poll;
			}
		}

		if(isset($user_budget['gift']) && !$complete)
		{
			$gift_poll = floatval($user_budget['gift']);
			if(($gift_poll - $price) >= 0)
			{
				$must_minus = $price;
				$complete = true;
			}
			else
			{
				$must_minus = $gift_poll;
			}

			if($must_minus)
			{
				transactions::set("add:poll:minus:gift", $_args['user_id'], ['minus' => $must_minus]);
				$price = $price - $gift_poll;
			}
		}

		if(isset($user_budget['prize']) && !$complete)
		{
			$prize_poll = floatval($user_budget['prize']);
			if(($prize_poll - $price) >= 0)
			{
				$must_minus = $price;
				$complete = true;
			}
			else
			{
				$must_minus = $prize_poll;
			}

			if($must_minus)
			{
				transactions::set("add:poll:minus:prize", $_args['user_id'], ['minus' => $must_minus]);
				$price = $price - $prize_poll;
			}
		}

		if(isset($user_budget['transfer']) && !$complete)
		{
			$transfer_poll = floatval($user_budget['transfer']);
			if(($transfer_poll - $price) >= 0)
			{
				$must_minus = $price;
				$complete = true;
			}
			else
			{
				$must_minus = $transfer_poll;
			}

			if($must_minus)
			{
				transactions::set("add:poll:minus:transfer", $_args['user_id'], ['minus' => $must_minus]);
				$price = $price - $transfer_poll;
			}
		}


		// free_account
		// free_add_poll
		// free_add_brand
		// free_add_filter
		// free_add_member
		// add_poll_cats
	}
}
?>