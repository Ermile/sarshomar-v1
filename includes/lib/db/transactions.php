<?php
namespace lib\db;
use \lib\debug;
use \lib\utility;

/** transactions managing **/
class transactions
{

	use transactions\set;
	use transactions\get;
	use transactions\budget;
	use transactions\poll;


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
			'post_id'            => null,
			'type'               => null,
			'unit_id'            => null,
			'plus'               => null,
			'minus'              => null,
			'budgetbefore'       => null,
			'budget'             => null,
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