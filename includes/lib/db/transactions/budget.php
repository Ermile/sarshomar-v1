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
	public static function budget($_user_id, $_type = null)
	{
		$only_one_value = false;
		$field = ['type','budget'];

		if($_type)
		{
			$only_one_value = true;
			$field          = 'budget';
			$query =
			"
				SELECT budget
				FROM transactions
				WHERE
					transactions.user_id = $_user_id AND
					transactions.type    = '$_type'
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
				ORDER BY id DESC
				LIMIT 1)

				UNION ALL (
					SELECT budget, 'real' AS `type`
					FROM transactions
					WHERE
						transactions.user_id = $_user_id AND
						transactions.type    = 'real'
					ORDER BY id DESC
					LIMIT 1)

				UNION ALL (
					SELECT budget, 'prize' AS `type`
					FROM transactions
					WHERE
						transactions.user_id = $_user_id AND
						transactions.type    = 'prize'
					ORDER BY id DESC
					LIMIT 1)

				UNION ALL (
					SELECT budget, 'transfer' AS `type`
					FROM transactions
					WHERE
						transactions.user_id = $_user_id AND
						transactions.type    = 'transfer'
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
	public static function budget_gift($_user_id)
	{
		return self::budget($_user_id, 'gift');
	}


	public static function budget_real($_user_id)
	{
		return self::budget($_user_id, 'real');
	}
}
?>