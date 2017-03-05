<?php
namespace lib\db\polls;
use \lib\debug;

trait fav_like
{

	/**
	 * set the favourites or liked the poll
	 */
	public static function fav_like($_type, $_user_id, $_poll_id, $_options = [])
	{
		$default_options =
		[
			'set_or_unset' => null,
			'debug'        => true,
		];

		$_options = array_merge($default_options, $_options);

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
			if(isset($poll['user_id']) && $poll['user_id'] == $_user_id)
			{
				// no problem to set fav and like
			}
			else
			{
				return debug::error(T_("Poll has not publisheded"), 'id', 'arguments');
			}
		}

		// if(!isset($poll['privacy']) || (isset($poll['privacy']) && $poll['privacy'] != 'public'))
		// {
		// 	return debug::error(T_("Poll not public"), 'id', 'arguments');
		// }

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
			'post_id'      => $_poll_id,
			'user_id'      => $_user_id,
			'option_cat'   => $cat,
			'option_key'   => $_type,
			'option_value' => $_poll_id,
			'limit'        => 1,
		];

		$where = $args;

		$exist_option_record = \lib\db\options::get($args);

		unset($args['limit']);
		unset($where['limit']);

		if(!$exist_option_record)
		{
			if($_options['set_or_unset'] === null || $_options['set_or_unset'] === true)
			{
				$insert_option = \lib\db\options::insert($args);
				\lib\db\ranks::plus($_poll_id, $_type);
				if($_options['debug'])
				{
					return debug::true(T_(ucfirst($_type). " set"));
				}
			}
			else
			{
				if($_options['debug'])
				{
					return debug::true(T_(ucfirst($_type). " unset"));
				}
			}
		}
		else
		{
			if($_options['set_or_unset'] === null)
			{
				if(isset($exist_option_record['status']) && $exist_option_record['status'] == 'disable')
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
					\lib\db\ranks::plus($_poll_id, $_type);
					if($_options['debug'])
					{
						return debug::true(T_(ucfirst($_type). " set"));
					}
				}
				else
				{
					if($_options['debug'])
					{
						return debug::true(T_(ucfirst($_type). " unset"));
					}
				}
			}
			elseif($_options['set_or_unset'] === true)
			{
				$args['option_status'] = 'enable';
				$result = \lib\db\options::update_on_error($args, $where);
				\lib\db\ranks::plus($_poll_id, $_type);
				if($_options['debug'])
				{
					return debug::true(T_(ucfirst($_type). " set"));
				}
			}
			elseif($_options['set_or_unset'] === false)
			{
				$args['option_status'] = 'disable';
				$result = \lib\db\options::update_on_error($args, $where);
				if($_options['debug'])
				{
					return debug::true(T_(ucfirst($_type). " unset"));
				}
			}
		}
		if($_options['debug'])
		{
			return debug::error("Syntax error", false, 'system');
		}
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
			'option_status' => 'enable',
			'limit'         => 1,
		];
		$exist_option_record = \lib\db\options::get($args);

		if(isset($exist_option_record['status']))
		{
			if($exist_option_record['status'] == 'disable')
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
	public static function is_fav()
	{
		return self::is_fav_like("fav", ...func_get_args());
	}


	/**
	 * check and return true is the poll is a favourites of the user
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function is_like()
	{
		return self::is_fav_like("like", ...func_get_args());
	}

	/**
	 * fav a poll
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function fav()
	{
		return self::fav_like("fav", ...func_get_args());
	}


	/**
	 * like poll
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function like()
	{
		return self::fav_like("like", ...func_get_args());
	}
}
?>