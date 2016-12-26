<?php
namespace lib\db\polls;

trait favo_like
{

	/**
	 * set the favourites or liked the poll
	 */
	public static function favo_like($_type, $_user_id, $_poll_id)
	{
		if(!$_user_id || !$_poll_id)
		{
			return false;
		}

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

		$insert_option = \lib\db\options::insert($args);
		if(!$insert_option)
		{
			$where = $args;

			array_splice($where, -1);

			$exist_option_record = \lib\db\options::get($where);

			if(isset($exist_option_record[0]['status']) && $exist_option_record[0]['status'] == 'disable')
			{
				$args['option_status'] = 'enable';
			}
			else
			{
				$args['option_status'] = 'disable';
			}
			return \lib\db\options::update_on_error($args, $where);
		}
		return $insert_option;
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
	public static function is_favo_like($_type, $_user_id, $_poll_id)
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
	public static function is_favo($_user_id, $_poll_id)
	{
		return self::is_favo_like("favourites", ...func_get_args());
	}


	/**
	 * check and return true is the poll is a favourites of the user
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function is_like($_user_id, $_poll_id)
	{
		return self::is_favo_like("like", ...func_get_args());
	}
}
?>