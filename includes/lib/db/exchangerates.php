<?php
namespace lib\db;

/** exchangerates managing **/
class exchangerates
{
	/**
	 * Gets the rate.
	 *
	 * @param      <type>  $_from  The from
	 * @param      <type>  $_to    { parameter_description }
	 */
	public static function get_from_to($_from, $_to)
	{
		$query =
		"
			SELECT
				*
			FROM
				exchangerates
			WHERE
				exchangerates.from = $_from AND
				exchangerates.to   = $_to
			ORDER BY id DESC
			LIMIT 1
		";
		$result = \lib\db::get($query, null, true);
		return $result;
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_id    The identifier
	 */
	public static function get($_id = null)
	{
		$id = null;
		if($_id)
		{
			$id = " WHERE id = $_id ";
		}
		$query = "SELECT * FROM exchangerates $id";
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
	 * insert new record of exchangerates
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert($_args)
	{
		$default_args =
		[
			'from'         => null,
			'to'           => null,
			'rate'         => null,
			'roundtype'    => null,
			'round'        => null,
			'wagestatic'   => null,
			'wage'         => null,
			'status'       => null,
			'desc'         => null,
			'meta'         => null,
			'createdate'   => null,
			'datemodified' => null,
			'enddate'      => null,
		];

		$_args = array_merge($default_args, $_args);

		$set = [];
		foreach ($_args as $field => $value)
		{
			if($value === null)
			{
				$set[] = " exchangerates.$field = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " exchangerates.$field = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " exchangerates.$field = '$value' ";
			}
		}
		$set = implode(",", $set);

		$query =
		"
			INSERT INTO
				exchangerates
			SET
				$set
		";
		$result = \lib\db::query($query);
		return $result;
	}
/**
	 * update record of exchangerates
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function update($_args, $_id)
	{
		$default_args =
		[
			'from'         => null,
			'to'           => null,
			'rate'         => null,
			'roundtype'    => null,
			'round'        => null,
			'wagestatic'   => null,
			'wage'         => null,
			'status'       => null,
			'desc'         => null,
			'meta'         => null,
			'createdate'   => null,
			'datemodified' => null,
			'enddate'      => null,
		];
		$_args = array_merge($default_args, $_args);

		$set = [];
		foreach ($_args as $field => $value)
		{
			if($value === null)
			{
				$set[] = " exchangerates.$field = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " exchangerates.$field = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " exchangerates.$field = '$value' ";
			}
		}
		$set = implode(",", $set);

		$query =
		"
			UPDATE
				exchangerates
			SET
				$set
			WHERE
				exchangerates.id = $_id
		";

		$result = \lib\db::query($query);
		return $result;
	}
}
?>