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
	public static function get($_from, $_to)
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
}
?>