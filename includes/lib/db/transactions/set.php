<?php
namespace lib\db\transactions;

trait set
{

	/**
	 * set a record of transactions
	 *
	 * @param      <type>  $_caller  The caller
	 */
	public static function set($_user_id, $_caller, $_options = [])
	{
		// get the transactions items by caller
		$item = \lib\db\transactionitems::caller($_caller);
		if(!$item)
		{
			debug::error(T_("Caller not found"));
			return false;
		}

		// get the user unit
		$user_unit = \lib\db\units::user_unit($_user_id);
		if(!$user_unit)
		{
			debug::error(T_("User unit not found"));
			return false;
		}

		// get the unit id
		$user_unit_id = \lib\db\units::get_id($user_unit);
		if(!$user_unit_id)
		{
			debug::error(T_("User unit id not found"));
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
			debug::error(T_("Transactio items id not found"));
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
		else
		{
			debug::error(T_("Transactio type not found"));
			return false;
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

		if(!$minus && !$plus)
		{
			if(isset($_options['plus']))
			{
				$plus = floatval($_options['plus']);
			}

			if(isset($_options['minus']))
			{
				$minus = floatval($_options['minus']);
			}
		}

		// get the budge befor
		$budget_befor = self::budget($_user_id, $type);
		// new budget by $budget_befor + plus - minus
		$exchange_id = null;
		if($force_change)
		{
			if($unit_id && $user_unit_id)
			{
				$from          = $unit_id;
				$to            = $user_unit_id;
				$exchange_rate = \lib\db\exchangerates::get($from, $to);
				if($exchange_rate)
				{
					if(isset($exchange_rate['rate']) && isset($exchange_rate['id']))
					{
						$rate        = (float) $exchange_rate['rate'];
						$value_from  = $plus - $minus;
						$value_to    = ($plus - $minus) * $rate;
						$new_budget  = $budget_befor + $value_to;
						$exchange_id = \lib\db\exchanges::set($exchange_rate['id'], $value_from, $value_to);
					}
					else
					{
						debug::error(T_("Exchange rate or id not found"));
						return false;
					}
				}
				else
				{
					debug::error(T_("Exchange rate not found"));
					return false;
				}
			}
			else
			{
				debug::error(T_("Unit id or user unit not found"));
				return false;
			}
		}
		else
		{
			$new_budget  = $budget_befor + $plus - $minus;
		}

		$status   = "enable";

		$finished = "no";
		if($type == 'gift')
		{
			$finished = "yes";
		}

		$arg =
		[
			'title'              => $title,
			'transactionitem_id' => $item_id,
			'user_id'            => $_user_id,
			'type'               => $type,
			'unit_id'            => $user_unit_id,
			'plus'               => ($plus) ? $plus : null,
			'minus'              => ($minus) ? $minus : null,
			'budgetbefore'       => $budget_befor,
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

}
?>