<?php
namespace lib\db;

/** transactionitems managing **/
class transactionitems
{

	/**
	 * insert new record of transactionitems
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert($_args)
	{
		$default_args =
		[
			'title'        => null,
			'caller'       => null,
			'unit_id'      => null,
			'type'         => null,
			'minus'        => null,
			'plus'         => null,
			'autoverify'   => 'no',
			'forcechange'  => 'no',
			'desc'         => null,
			'meta'         => null,
			'status'       => null,
			'enddate'      => null,
		];
		$_args = array_merge($default_args, $_args);

		$query =
		"
			INSERT INTO
				transactionitems
			SET
				transactionitems.title        = '$_args[title]',
				transactionitems.caller       = '$_args[caller]',
				transactionitems.unit_id      = '$_args[unit_id]',
				transactionitems.type         = '$_args[type]',
				transactionitems.minus        = '$_args[minus]',
				transactionitems.plus         = '$_args[plus]',
				transactionitems.autoverify   = '$_args[autoverify]',
				transactionitems.forcechange  = '$_args[forcechange]',
				transactionitems.desc         = '$_args[desc]',
				transactionitems.meta         = '$_args[meta]',
				transactionitems.status       = '$_args[status]',
				transactionitems.enddate      = '$_args[enddate]'
		";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * get all transactionitems
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_id = null)
	{
		$id = null;
		if($_id)
		{
			$id = " WHERE id = $_id ";
		}
		$query = "SELECT * FROM transactionitems $id";
		if($id)
		{
			$result = \lib\db::get($query, null, true);
		}
		else
		{
			$result = \lib\db::get($query);
		}
		return $result;
	}


	/**
	 * update record of transactionitems
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update($_args, $_id)
	{
		$default_args =
		[
			'title'        => null,
			'caller'       => null,
			'unit_id'      => null,
			'type'         => null,
			'minus'        => null,
			'plus'         => null,
			'autoverify'   => 'no',
			'forcechange'  => 'no',
			'desc'         => null,
			'meta'         => null,
			'status'       => null,
			'enddate'      => null,
		];
		$_args = array_merge($default_args, $_args);

		$query =
		"
			UPDATE
				transactionitems
			SET
				transactionitems.title        = '$_args[title]',
				transactionitems.caller       = '$_args[caller]',
				transactionitems.unit_id      = '$_args[unit_id]',
				transactionitems.type         = '$_args[type]',
				transactionitems.minus        = '$_args[minus]',
				transactionitems.plus         = '$_args[plus]',
				transactionitems.autoverify   = '$_args[autoverify]',
				transactionitems.forcechange  = '$_args[forcechange]',
				transactionitems.desc         = '$_args[desc]',
				transactionitems.meta         = '$_args[meta]',
				transactionitems.status       = '$_args[status]',
				transactionitems.enddate      = '$_args[enddate]'
			WHERE
				transactionitems.id = $_id
		";
		$result = \lib\db::query($query);
		return $result;
	}
}
?>