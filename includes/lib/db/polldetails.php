<?php
namespace lib\db;

/** polldetails managing **/
class polldetails
{
	/**
	 * this library work with polldetails
	 * v1.0
	 */


	/**
	 * insert new tag in polldetails table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{

		if(empty($_args))
		{
			return null;
		}
		$set = [];
		foreach ($_args as $key => $value) {
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			else
			{
				$set[] = " `$key` = '$value' ";
			}
		}
		$set = join($set, ',');

		$query =
		"
			INSERT INTO
				polldetails
			SET
				$set
		";
		return \lib\db::query($query);
	}


	/**
	 * insert multi value to polldetails
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi($_args)
	{
		// marge all input array to creat list of field to be insert
		$fields = [];
		foreach ($_args as $key => $value) {
			$fields = array_merge($fields, $value);
		}

		// creat multi insert query : INSERT INTO TABLE (FIELDS) VLUES (values), (values), ...
		$values = [];
		$together = [];
		foreach ($_args	 as $key => $value) {
			foreach ($fields as $field_name => $vain) {
				if(array_key_exists($field_name, $value)){
					$values[] = "'" . trim($value[$field_name]) . "'";
				}else{
					$values[] = "NULL";
				}
			}
			$together[] = join($values, ",");
			$values = [];
		}

		if(empty($fields))
		{
			return null;
		}

		$fields = join(array_keys($fields), ",");

		$values = join($together, "),(");

		// crate string query
		$query = "
				INSERT INTO polldetails
				($fields)
				VALUES
				($values)
				";

		return \lib\db::query($query);
	}


	/**
	 * update field from polldetails table
	 * get fields and value to update
	 * @example update table set field = 'value' , field = 'value' , .....
	 * @param array $_args fields data
	 * @param string || int $_id record id
	 * @return mysql result
	 */
	public static function update($_args, $_id)
	{

		$query = [];
		foreach ($_args as $field => $value)
		{
			$query[] = "$field = '$value'";
		}
		$query = join($query, ",");

		// make update query
		$query = "
				UPDATE polldetails
				SET $query
				WHERE polldetails.id = $_id;
				";

		return \lib\db::query($query);

	}


	/**
	 * get string query and return mysql result
	 * @param string $_query string query
	 * @return mysql result
	 */
	public static function select($_query, $_type = 'query')
	{
		return \lib\db::$_type($_query);
	}


	/**
	 * Gets the user count of answered or skipped
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_type     The type
	 */
	public static function user_total($_user_id, $_type = null)
	{
		switch ($_type) {
			case 'answered':
				$opt = " AND polldetails.opt != '0' ";
				break;
			case 'skipped':
				$opt = " AND polldetails.opt = '0' ";
				break;
			default:
				$opt = "";
				break;
		}

		$query =
		"
			SELECT
				COUNT(polldetails.id) AS 'count'
			FROM
				polldetails
			WHERE
				user_id = $_user_id
				$opt
		";
		$result = \lib\db::get($query, 'count', true);
		return $result;
	}

	public static function user_total_answered($_user_id)
	{
		return self::user_total($_user_id, "answered");
	}

	public static function user_total_skipped($_user_id)
	{
		return self::user_total($_user_id, "skipped");
	}


	public static function people($_poll_ids, $_type = null)
	{
		switch ($_type) {
			case 'answered':
				$opt = " AND polldetails.opt != '0' ";
				break;
			case 'skipped':
				$opt = " AND polldetails.opt = '0' ";
				break;
			default:
				$opt = "";
				break;
		}

		$poll_ids = join($_poll_ids, ",");
		$query =
		"
			SELECT
				COUNT(polldetails.id) AS 'count'
			FROM
				polldetails
			WHERE
				polldetails.post_id IN ($poll_ids)
				$opt
		";
		$result = \lib\db::get($query, 'count', true);
		return $result;
	}

	public static function people_answered($_poll_ids)
	{
		return self::people($_poll_ids, "answered");
	}

	public static function people_skipped($_poll_ids)
	{
		return self::people($_poll_ids, "skipped");
	}
}
?>