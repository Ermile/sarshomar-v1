<?php
namespace lib\db\transactions;
use \lib\debug;
use \lib\utility;

trait budget
{
	/**
	 * get the budget of users
	 *
	 * @param      <type>  $_user_id  The user identifier
	 */
	public static function budget($_user_id, $_options = [])
	{

		$default_options =
		[
			'type' => null,
			'unit' => null,
		];
		if(!is_array($_options))
		{
			$_options = [];
		}
		$_options = array_merge($default_options, $_options);

		if($_options['unit'] === 'all')
		{
			$all_unit =
			"SELECT transactions.budget AS `budget`, units.title AS `unit` FROM transactions
			INNER JOIN units ON transactions.unit_id = units.id
			WHERE transactions.id IN (
			SELECT MAX(transactions.id) FROM transactions
			WHERE transactions.user_id = $_user_id
			GROUP BY transactions.unit_id)
			-- get all budget in all units of users
			";
			$all_unit =  \lib\db::get($all_unit, ['unit', 'budget']);
			return $all_unit;
		}

		$unit = null;
		if(isset($_options['unit']) && is_numeric($_options['unit']))
		{
			$unit = " AND transactions.unit_id = $_options[unit] ";
		}

		$only_one_value = false;
		$field = ['type','budget'];

		if($_options['type'])
		{
			$only_one_value = true;
			$field          = 'budget';
			$query =
			"
				SELECT budget
				FROM transactions
				WHERE
					transactions.user_id = $_user_id AND
					transactions.type    = '$_options[type]'
					$unit
				ORDER BY id DESC
				LIMIT 1
			";
		}
		else
		{
			$query =
			"
				(SELECT budget, 'gift' AS `type`
				FROM transactions
				WHERE
					transactions.user_id = $_user_id AND
					transactions.type    = 'gift'
					$unit
				ORDER BY id DESC
				LIMIT 1)

				UNION ALL (
					SELECT budget, 'real' AS `type`
					FROM transactions
					WHERE
						transactions.user_id = $_user_id AND
						transactions.type    = 'real'
						$unit
					ORDER BY id DESC
					LIMIT 1)

				UNION ALL (
					SELECT budget, 'prize' AS `type`
					FROM transactions
					WHERE
						transactions.user_id = $_user_id AND
						transactions.type    = 'prize'
						$unit
					ORDER BY id DESC
					LIMIT 1)

				UNION ALL (
					SELECT budget, 'transfer' AS `type`
					FROM transactions
					WHERE
						transactions.user_id = $_user_id AND
						transactions.type    = 'transfer'
						$unit
					ORDER BY id DESC
					LIMIT 1)
			";

		}
		$result = \lib\db::get($query, $field, $only_one_value);
		if(!$result)
		{
			return 0;
		}
		return $result;
	}


	/**
	 * get the gitf budget
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function budget_gift($_user_id, $_options = [])
	{
		return self::budget($_user_id, $_options);
	}


	public static function budget_real($_user_id, $_options = [])
	{
		return self::budget($_user_id, $_options);
	}
}
?>