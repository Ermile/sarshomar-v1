<?php
namespace lib\db;

/** transactions managing **/
class transactions
{
	/**
	 * set a record of transactions
	 *
	 * @param      <type>  $_caller  The caller
	 */
	public static function set($_user_id, $_caller)
	{
		// get the transactions items by caller
		$item = \lib\db\transactionitems::caller($_caller);
		if(!$item)
		{
			return false;
		}

		// get the user unit
		$user_unit = \lib\db\units::user_unit($_user_id);
		if(!$user_unit)
		{
			return false;
		}

		// get the unit id
		$user_unit_id = \lib\db\units::get_id($user_unit);
		if(!$user_unit_id)
		{
			return false;
		}
		else
		{
			$user_unit_id = (int) $user_unit_id;
		}

		// get the unit id
		$unit_id = false;
		if(isset($item['unit_id']))
		{
			$unit_id = (int) $item['unit_id'];
		}


		// check this items is a force change items ?
		$force_change = false;
		if(isset($item['forcechange']) && $item['forcechange'] == 'yes')
		{
			$force_change = true;
		}

		// check this items is a auto verify items ?
		$auto_verify  = false;
		if(isset($item['autoverify']) && $item['autoverify'] == 'yes')
		{
			$auto_verify = true;
		}

		// get the item id
		$item_id  = false;
		if(isset($item['id']))
		{
			$item_id = $item['id'];
		}
		else
		{
			return false;
		}

		// get the item title
		$title  = false;
		if(isset($item['title']))
		{
			$title = $item['title'];
		}

		// get the item type
		$type  = false;
		if(isset($item['type']))
		{
			$type = $item['type'];
		}

		$minus = 0;
		if(isset($item['minus']) && $item !== null)
		{
			$minus = (float) $item['minus'];
		}

		$plus = 0;
		if(isset($item['plus']) && $item !== null)
		{
			$plus = (float) $item['plus'];
		}

		// get the budge befor
		$budget_befor = self::budget($_user_id);
		// new budget by $budget_befor + plus - minus
		$new_budget  = $budget_befor + $plus - $minus;
		$exchange_id = null;
		if($force_change)
		{
			if($unit_id)
			{
				$from          = $unit_id;
				$to            = $user_unit_id;
				$exchange_rate = \lib\db\exchangerates::get($from, $to);
				if($exchange_rate)
				{
					if(isset($exchange_rate['rate']) && isset($exchange_rate['id']))
					{
						$rate       = (float) $exchange_rate['rate'];
						$value_from = $new_budget;
						$value_to   = $new_budget * $rate;
						$exchange_id  = \lib\db\exchanges::set($exchange_rate['id'], $value_from, $value_to);
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		exit();

		$status   = "enable";
		$finished = "no";

		$arg =
		[
			'title'              => $title,
			'transactionitem_id' => $item_id,
			'user_id'            => $_user_id,
			'type'               => $type,
			'unit_id'            => $user_unit_id,
			'plus'               => ($plus) ? $plus : null,
			'minus'              => ($minus) ? $minus : null,
			'budgetbefor'        => $budget_befor,
			'budget'             => $new_budget,
			'exchange_id'        => $exchange_id,
			'status'             => $status,
			'meta'               => null,
			'desc'               => null,
			'related_user_id'    => null,
			'parent_id'          => null,
			'finished'           => 'no',
		];
		$insert = self::insert($arg);
		return $insert;
	}


	/**
	 * get the budget of users
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function budget($_user_id)
	{
		$query = "SELECT budget FROM transactions WHERE user_id = $_user_id ORDER BY id DESC LIMIT 1";
		$result = \lib\db::get($query, 'budget', true);
		if(!$result)
		{
			return 0;
		}
		return $result;
	}


	/**
	 * insert new record of transactions
	 *
	 * @param      <type>  $_arg   The argument
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function insert($_arg)
	{
		$defult_args =
		[
			'title'              => null,
			'transactionitem_id' => null,
			'user_id'            => null,
			'type'               => null,
			'unit_id'            => null,
			'plus'               => null,
			'minus'              => null,
			'budgetbefor'        => null,
			'budget'             => null,
			'exchange_id'        => null,
			'status'             => null,
			'meta'               => null,
			'desc'               => null,
			'related_user_id'    => null,
			'parent_id'          => null,
			'finished'           => 'no',
		];
		$_arg = array_merge($defult_args, $_arg);

		$set = [];
		foreach ($_arg as $field => $value)
		{
			if($value === null)
			{
				$set[] = " transactions.$field = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " transactions.$field = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " transactions.$field = '$value' ";
			}
		}
		$set = implode(",", $set);

		$query =
		"
			INSERT INTO
				transactions
			SET
				$set
		";
		$result = \lib\db::query($query);
		return $result;
	}
}
?>