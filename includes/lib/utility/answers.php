<?php
namespace lib\utility;

class answers
{
	use answers\get;
	use answers\insert;
	use answers\save;
	use answers\update;


	/**
	 * return the status array
	 *
	 * @param      <type>   $_status  The status
	 * @param      boolean  $_update  The update
	 * @param      array    $_msg     The message
	 */
	public static function status($_status, $_opt, $_msg = null)
	{
		$opt_index = null;
		if(is_string($_opt))
		{
			$opt_index = explode("_", $_opt);
			$opt_index = end($opt_index);
		}
		return
		[
			'status'    => $_status,
			'opt'       => $_opt,
			'opt_index' => (int) $opt_index,
			'msg'       => $_msg
		];
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_poll_id  The poll identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function delete($_poll_id)
	{
		$query =
		"
			UPDATE
				options
			SET
				options.option_status = 'disable'
			WHERE
				options.post_id = $_poll_id AND
				options.option_key LIKE 'opt%' AND
				options.user_id IS NULL
			-- answers::delete()
		";
		return \lib\db::query($query);
	}


	/**
	 * real delete record from database
	 *
	 * @param      <type>  $_where_or_id  The where or identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function hard_delete($_where_or_id)
	{
		if(is_numeric($_where_or_id))
		{
			$where = " options.id = $_where_or_id ";
		}
		elseif(is_array($_where_or_id))
		{
			$tmp = [];
			foreach ($_where_or_id as $key => $value)
			{
				if(preg_match("/\%/", $value))
				{
					$tmp[] = " $key LIKE '$value' ";
				}
				else
				{
					$tmp[] = " $key = '$value' ";
				}
			}
			$where = join($tmp, " AND ");
		}
		else
		{
			return false;
		}

		$query = " DELETE FROM	options	WHERE $where -- answers::hard_delete() ";
		return \lib\db::query($query);
	}


	/**
	 * check the user answered to this poll or no
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function is_answered($_user_id, $_poll_id)
	{
		$query =
		"
			SELECT
				*
			FROM
				polldetails
			WHERE
				user_id = $_user_id AND
				post_id = $_poll_id
			LIMIT 1
			-- answers::is_answered()
			-- check user is answered to this poll or no
		";
		$result = \lib\db::get($query, null, true);
		if($result)
		{
			return $result;
		}
		return false;
	}
}
?>