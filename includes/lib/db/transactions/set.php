<?php
namespace lib\db\transactions;
use \lib\debug;
use \lib\utility;

trait set
{

	/**
	 * set a record of transactions
	 *
	 * @param      <type>  $_caller  The caller
	 */
	public static function set($_caller, $_user_id, $_options = [])
	{

		$debug = true;
		if(array_key_exists('debug', $_options))
		{
			if(!$_options['debug'])
			{
				$debug = false;
			}
		}

		$post_id = null;
		if(isset($_options['post_id']))
		{
			$post_id = (int) $_options['post_id'];
		}

		$log_meta =
		[
			'data' => $post_id,
			'meta' =>
			[
				'args'    => func_get_args(),
				'session' => $_SESSION,
				'caller'  => $_caller,
			]
		];

		// get the transactions items by caller
		$item = \lib\db\transactionitems::caller($_caller);
		if(!$item)
		{
			\lib\db\logs::set('transaction:caller:not:found', $_user_id, $log_meta);
			if($debug)
			{
				debug::error(T_("Caller not found"));
			}
			return false;
		}

		$transactionitems_unit_id = null;
		if(isset($item['unit_id']))
		{
			$transactionitems_unit_id = (int) $item['unit_id'];
		}
		else
		{
			\lib\db\logs::set('transactionitems:unit:not:found', $_user_id, $log_meta);
			return false;
		}

		// get the user unit
		$user_unit = \lib\db\units::find_user_unit($_user_id, true);
		if(!$user_unit)
		{
			\lib\db\logs::set('transaction:user:unit:not:found', $_user_id, $log_meta);
			$user_unit = 'sarshomar';
			// return false;
		}

		// get the unit id
		$user_unit_id = \lib\db\units::get_id($user_unit);
		if(!$user_unit_id)
		{
			\lib\db\logs::set('transaction:user:unit:id:not:found', $_user_id, $log_meta);
			if($debug)
			{
				debug::error(T_("User unit id not found"));
			}
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
		if(isset($item['forcechange']) && $item['forcechange'] === 'yes')
		{
			$force_change = true;
		}

		// check this items is a auto verify items ?
		$auto_verify  = false;
		if(isset($item['autoverify']) && $item['autoverify'] === 'yes')
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
			\lib\db\logs::set('transaction:transaction:items:id:not:found', $_user_id, $log_meta);
			if($debug)
			{
				debug::error(T_("Transaction items id not found"));
			}
			return false;
		}

		// get the item title
		$title  = null;
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
			\lib\db\logs::set('transaction:transaction:type:not:found', $_user_id, $log_meta);
			if($debug)
			{
				debug::error(T_("Transaction type not found"));
			}
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

		$save_transaction_by_unit = $transactionitems_unit_id;

		$exchange_id = null;

		if($force_change)
		{
			$budget_befor = self::budget($_user_id, ['type' => $type, 'unit' => $user_unit_id]);

			if($transactionitems_unit_id && $user_unit_id)
			{
				$from          = $transactionitems_unit_id;
				$to            = $user_unit_id;

				if(intval($from) === intval($to))
				{
					$exchange_rate = ['rate' => 1];
				}
				else
				{
					$exchange_rate = \lib\db\exchangerates::get_from_to($from, $to);
				}

				if($exchange_rate || $user_unit === 'sarshomar')
				{
					$rate = 1;
					if(array_key_exists('rate', $exchange_rate))
					{
						$rate = floatval($exchange_rate['rate']);
					}
					$minus      = $minus * $rate;
					$plus       = $plus * $rate;
					$value_to   = ($plus - $minus);
					$new_budget = $budget_befor + $value_to;

					$save_transaction_by_unit = $user_unit_id;

				}
				else
				{
					\lib\db\logs::set('transaction:exchange:rate:not:found', $_user_id, $log_meta);
					if($debug)
					{
						debug::error(T_("Exchange rate not found"));
					}
					return false;
				}
			}
			else
			{
				\lib\db\logs::set('transaction:unit:id:or:user:unit:not:found', $_user_id, $log_meta);
				if($debug)
				{
					debug::error(T_("Unit id or user unit not found"));
				}
				return false;
			}
		}
		else
		{
			// get the budge befor
			$budget_befor = self::budget($_user_id, ['type' => $type, 'unit' => $transactionitems_unit_id]);
			// new budget by $budget_befor + plus - minus
			$new_budget  = $budget_befor + $plus - $minus;
		}

		$status   = "enable";
		$finished = "no";
		if($type === 'gift')
		{
			$finished = "yes";
		}

		$arg =
		[
			'title'              => $title,
			'transactionitem_id' => $item_id,
			'user_id'            => $_user_id,
			'post_id'            => $post_id,
			'type'               => $type,
			'unit_id'            => $save_transaction_by_unit,
			'plus'               => ($plus) ? $plus : null,
			'minus'              => ($minus) ? $minus : null,
			'budgetbefore'       => $budget_befor,
			'budget'             => $new_budget,
			// 'exchange_id'        => $exchange_id,
			'status'             => $status,
			'meta'               => null,
			'desc'               => null,
			'related_user_id'    => null,
			'parent_id'          => (isset($_options['parent_id'])) ? $_options['parent_id'] : null,
			'finished'           => 'no',
		];
		$insert = self::insert($arg);

		\lib\db\logs::set($_caller, $_user_id, $log_meta);

		return $insert;
	}

}
?>