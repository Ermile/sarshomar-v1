<?php
namespace lib\db;

/** userdashboards managing **/
class userdashboards
{
	/**
	 * user dashboard field
	 *
	 * @var        array
	 */
	public static $dashboard_field =
	[
		'poll_answered',
		'poll_skipped',
		'survey_answered',
		'survey_skipped',
		'my_poll',
		'my_survey',
		'my_poll_answered',
		'my_poll_skipped',
		'my_survey_answered',
		'my_survey_skipped',
		'user_referred',
		'user_verified',
		'comment_count',
		'draft_count',
		'publish_count',
		'awaiting_count',
		'my_like',
		'my_fav',
	];


	/**
	 * set first record of user dashboard
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function set($_user_id)
	{
		$fields = self::$dashboard_field;

		$set = [];
		foreach ($fields as $key => $value)
		{
			$set[] = " userdashboards.$value = NULL ";
		}

		$set = implode(",", $set);

		$query = "INSERT INTO userdashboards SET userdashboards.user_id = $_user_id, $set ";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * get dashboard data
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      string  $_field    The field
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_user_id, $_field = null)
	{
		$field     = '*';
		$get_field = null;
		if(is_array($_field))
		{
			$field     = '`'. join($_field, '`, `'). '`';
			$get_field = null;
		}
		elseif($_field && is_string($_field))
		{
			$field     = '`'. $_field. '`';
			$get_field = $_field;
		}

		$query = "SELECT $field FROM userdashboards WHERE userdashboards.user_id = $_user_id LIMIT 1 -- userdashboards::get() ";
		$result = \lib\db::get($query, $get_field, true);
		return $result;
	}


	/**
	 * change poll userdashboards
	 *
	 * @param      <type>   $_user_id  The poll identifier
	 * @param      <type>   $_field    The field
	 * @param      integer  $_plus     The plus
	 * @param      array    $_options  The options
	 *
	 * @return     <type>   ( description_of_the_return_value )
	 */
	public static function change_dashboard($_user_id, $_field, $_plus = 1, $_options = [])
	{
		$default_options =
		[
			'replace' => false,
			'type'    => 'plus',
		];

		$_options        = array_merge($default_options, $_options);

		$plus  = true;
		$minus = false;

		if($_options['type'] === 'minus')
		{
			$plus  = false;
			$minus = true;
		}

		$replace = false;
		if($_options['replace'] === true)
		{
			$replace = true;
		}

		$userdashboards = self::get($_user_id);

		if(empty($userdashboards))
		{
			self::set($_user_id);
			$userdashboards               = self::$dashboard_field;
		}

		$sum    = 0;
		$update = [];
		foreach ($userdashboards as $key => $value)
		{
			switch ($key)
			{
				case 'id':
				case 'user_id':
					continue;
					break;

				default:
					if($key == $_field)
					{
						if($replace)
						{
							$update[] = " userdashboards.$key = $_plus ";
						}
						else
						{
							if($plus)
							{
								if(is_null($value))
								{
									$update[] = " userdashboards.$key = 1 ";
								}
								else
								{
									$update[] = " userdashboards.$key = userdashboards.$key + 1 ";
								}
							}
							else
							{
								if(is_null($value))
								{
									$update[] = " userdashboards.$key = 0 ";
								}
								else
								{
									$update[] = " userdashboards.$key = userdashboards.$key - 1 ";
								}
							}
						}
					}
					break;
			}
		}

		if(empty($update))
		{
			return;
		}

		$update = implode(",", $update);
		$query  = "UPDATE userdashboards SET $update WHERE user_id = $_user_id LIMIT 1 ";
		$result = \lib\db::query($query);
		return $result;
	}


	/**
	 * plus the field of userdashboards
	 *
	 * @param      <type>  $_user_id  The poll identifier
	 * @param      <type>  $_field    The field
	 */
	public static function plus($_user_id, $_field, $_plus = 1, $_options = [])
	{
		$default_options = ['type' => 'plus'];
		if(!is_array($_options))
		{
			$_options = [];
		}
		$_options = array_merge($default_options, $_options);
		return self::change_dashboard($_user_id, $_field, $_plus, $_options);
	}


	/**
	 * minus the poll userdashboards
	 *
	 * @param      <type>   $_user_id  The poll identifier
	 * @param      <type>   $_field    The field
	 * @param      integer  $_minus    The minus
	 * @param      array    $_options  The options
	 *
	 * @return     <type>   ( description_of_the_return_value )
	 */
	public static function minus($_user_id, $_field, $_minus = 1, $_options = [])
	{
		$default_options = ['type' => 'minus'];
		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);
		return self::change_dashboard($_user_id, $_field, $_minus, $_options);
	}
}
?>