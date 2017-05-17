<?php
namespace content_api\v1\poll\status\tools;
use \lib\db\transactions;
use \lib\utility;
use \lib\debug;
use \lib\db;

trait money
{
	use \lib\utility\money;
	/**
	 * Calculates the price.
	 */
	public function calc_price_old()
	{

		return true;

		if(self::check_api_permission('u:free_account:view'))
		{
			// return true;
		}

		if(self::check_api_permission('u:free_add_poll:view'))
		{
			// return true;
		}

		$poll_id = utility\shortURL::decode(utility::request('id'));

		$price   = 0;
		$price_array = [];
		if(true || !self::check_api_permission('u:sarshomar:view'))
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
			if(true || !self::check_api_permission('u:free_add_filter:view'))
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

		$user_budget = transactions::budget($this->user_id);
		$user_unit   = \lib\db\units::find_user_unit($this->user_id, true);
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
				transactions::set("add:poll:minus:real", $this->user_id, ['minus' => $must_minus]);
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
				transactions::set("add:poll:minus:gift", $this->user_id, ['minus' => $must_minus]);
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
				transactions::set("add:poll:minus:prize", $this->user_id, ['minus' => $must_minus]);
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
				transactions::set("add:poll:minus:transfer", $this->user_id, ['minus' => $must_minus]);
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