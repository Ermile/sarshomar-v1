<?php
namespace lib\db;

/** api_log managing **/
class apilogs
{
	/**
	 * insert new recrod in apilogs table
	 * @param array $_args fields data
	 * @return mysql result
	 */
	public static function insert($_args)
	{
		if(!is_array($_args))
		{
			return false;
		}

		$set = [];

		foreach ($_args as $key => $value)
		{
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			elseif(is_int($value))
			{
				$set[] = " `$key` = $value ";
			}
			elseif(is_string($value))
			{
				$set[] = " `$key` = '$value' ";
			}
		}
		if(empty($set))
		{
			return;
		}

		$set   = join($set, ',');

		$query = "INSERT IGNORE INTO apilogs SET $set ";

		\lib\db::query($query, \lib\db\logs::get_db_log_name(), ['resume_on_error' => true]);
		return true;
	}
}
?>