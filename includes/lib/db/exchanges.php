<?php
namespace lib\db;

/** exchanges managing **/
class exchanges
{

	/**
	 * set new record of exchanges
	 *
	 * @param      <type>  $_exchangerate_id  The exchangerate identifier
	 * @param      <type>  $_value_from       The value from
	 * @param      <type>  $_value_to         The value to
	 */
	public static function set($_exchangerate_id, $_value_from, $_value_to)
	{
		$query =
		"
			INSERT INTO
				exchanges
			SET
				exchangerate_id = $_exchangerate_id,
				valuefrom       = $_value_from,
				valueto         = $_value_to
		";
		return \lib\db::query($query);
	}
}
?>