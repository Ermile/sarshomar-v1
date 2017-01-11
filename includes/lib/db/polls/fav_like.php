<?php
namespace lib\db\polls;
use \lib\debug;

trait fav_like
{

	/**
	 * set the favourites or liked the poll
	 */
	public static function fav_like($_type, $_user_id, $_poll_id, $_set_or_unset = null)
	{

		if(!$_poll_id)
		{
			return debug::error(T_("Poll not selected"), 'id', 'arguments');
		}
		
		$poll = \lib\db\polls::get_poll($_poll_id);

		if(!$poll)
		{
			return debug::error(T_("Poll id not found"), 'id', 'arguments');
		}

		if(!isset($poll['status']) || (isset($poll['status']) && $poll['status'] != 'publish'))
		{
			return debug::error(T_("Poll not published"), 'id', 'arguments');
		}

		if(!isset($poll['privacy']) || (isset($poll['privacy']) && $poll['privacy'] != 'public'))
		{
			return debug::error(T_("Poll not public"), 'id', 'arguments');
		}

		if(!isset($poll['type']) || (isset($poll['type']) && $poll['type'] != 'poll' && $poll['type'] != 'survey'))
		{
			return debug::error(T_("The id is not a poll or survey"), 'id', 'arguments');
		}

		if(!$_user_id)
		{
			return debug::error(T_("User not found"));
		}

		$cat = 'user_detail_'. $_user_id;
		$args =
		[
			'post_id'       => $_poll_id,
			'user_id'       => $_user_id,
			'option_cat'    => $cat,
			'option_key'    => $_type,
			'option_value'  => $_poll_id
		];

		$where = $args;

		$exist_option_record = \lib\db\options::get($args);

		if(!$exist_option_record)
		{
			if($_set_or_unset === null || $_set_or_unset === true)
			{
				$insert_option = \lib\db\options::insert($args);
				return \lib\debug::true(T_(ucfirst($_type). " set"));
			}
			else
			{
				return \lib\debug::true(T_(ucfirst($_type). " unset"));
			}
		}
		else
		{
			if($_set_or_unset === null)
			{
				if(isset($exist_option_record[0]['status']) && $exist_option_record[0]['status'] == 'disable')
				{
					$args['option_status'] = 'enable';
				}
				else
				{
					$args['option_status'] = 'disable';
				}
				$result =\lib\db\options::update_on_error($args, $where);
				if($args['option_status'] == 'enable')
				{
					return \lib\debug::true(T_(ucfirst($_type). " set"));
				}
				else
				{
					return \lib\debug::true(T_(ucfirst($_type). " unset"));
				}
			}
			elseif($_set_or_unset === true)
			{
				$args['option_status'] = 'enable';
				$result = \lib\db\options::update_on_error($args, $where);
				return \lib\debug::true(T_(ucfirst($_type). " set"));
			}
			elseif($_set_or_unset === false)
			{
				$args['option_status'] = 'disable';
				$result = \lib\db\options::update_on_error($args, $where);
				return \lib\debug::true(T_(ucfirst($_type). " unset"));
			}
		}
		return debug::error("Syntax error", false, 'system');
	}


	/**
	 * check and return true if the poll is liked or favourites of user
	 *
	 * @param      <type>   $_user_id  The user identifier
	 * @param      <type>   $_poll_id  The poll identifier
	 * @param      <type>   $_type     The type
	 *
	 * @return     boolean  True if favo OR like, False otherwise.
	 */
	public static function is_fav_like($_type, $_user_id, $_poll_id)
	{
		$cat = 'user_detail_'. $_user_id;
		$args =
		[
			'post_id'       => $_poll_id,
			'user_id'       => $_user_id,
			'option_cat'    => $cat,
			'option_key'    => $_type,
			'option_value'  => $_poll_id,
			'option_status' => 'enable'
		];
		$exist_option_record = \lib\db\options::get($args);

		if(isset($exist_option_record[0]['status']))
		{
			if($exist_option_record[0]['status'] == 'disable')
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		return false;
	}


	/**
	 * check and return true is the poll is a favourites of the user
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function is_fav($_user_id, $_poll_id)
	{
		return self::is_fav_like("favourites", ...func_get_args());
	}


	/**
	 * check and return true is the poll is a favourites of the user
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function is_like($_user_id, $_poll_id)
	{
		return self::is_fav_like("like", ...func_get_args());
	}


	public static function fav($_user_id, $_poll_id, $_type = null)
	{
		return self::fav_like("favourites", ...func_get_args());
	}


	public static function like($_user_id, $_poll_id, $_type = null)
	{
		return self::fav_like("like", ...func_get_args());
	}
}
?>