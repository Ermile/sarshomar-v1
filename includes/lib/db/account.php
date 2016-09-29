<?php
namespace lib\db;

class account
{

	/**
	 * Gets the account data.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function get_account_data($_args)
	{
		if(isset($_args['user_id']))
		{
			$user_id = $_args['user_id'];
		}
		else
		{
			return false;
		}

		$query =
		"
			SELECT
				options.option_key 		AS 'key',
				options.option_value 	AS 'value',
				terms.term_title,
				terms.term_url
			FROM
				options
			LEFT JOIN terms ON
					terms.id = options.option_value AND
					options.option_cat = 'favorites'
			WHERE
				options.option_cat = 'user_detail_$user_id'
		";

		$result = \lib\db::get($query, ['key', 'value']);

		return $result;
	}


	public static function set_account_data($_user_id, $_args)
	{
		$displayname       = $_args['displayname'];
		$mobile            = $_args['mobile'];

		// process age and range
		$age               = $_args['age'];
		$range             = $_args['range'];

		$old_account_data = self::get_account_data(['user_id' => $_user_id]);

		$_args = array_filter($_args);

		$update_query = [];
		$run_all_query = true;
		foreach ($_args as $field => $value)
		{
			if(isset($old_account_data[$field]))
			{
				if($old_account_data[$field] != $value)
				{
					$where = "user_id = '$_user_id' AND option_cat = 'user_detail_$_user_id' AND option_key = '$field' ";
					$update_query = "UPDATE options SET options.option_value = '" . $_args[$field] . "' WHERE $where";
					$run_query = \lib\db::query($update_query);
					if($run_all_query)
					{
						$run_all_query = $run_query;
					}
				}
			}
			else
			{
				$value = $_args[$field];
				$insert =
				"
					INSERT INTO
						options
						(post_id, user_id, 	   option_cat, 				option_key,   option_value )
					VALUES
						(NULL, 	  '$_user_id',  'user_detail_$_user_id', 	'$field',	  '$value'	   )
				";
				$run_query = \lib\db::query($insert);
				if($run_all_query)
				{
					$run_all_query = $run_query;
				}
			}
		}
		return $run_all_query;
	}
}
?>