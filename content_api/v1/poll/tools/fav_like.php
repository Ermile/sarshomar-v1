<?php
namespace content_api\v1\poll\tools;
use \lib\debug;
use \lib\utility;

trait fav_like
{
	/**
	 * fav or like
	 * unfav or unlike
	 *
	 * @param      <type>  $_fav_like  The fav like
	 * @param      <type>  $_type      The type
	 */
	private function fav_like($_fav_like, $_type)
	{
		if(!preg_match("/^[". \lib\utility\shortURL::ALPHABET. "]+$/", utility::request("id")))
		{
			return debug::error(T_("Invalid parametr id"), 'id', 'arguments');
		}

		$poll_id = utility\shortURL::decode(utility::request('id'));
		return \lib\db\polls::$_fav_like($this->user_id, $poll_id, ['set_or_unset' => $_type]);
	}


	/**
	 * set fav or like
	 *
	 * @param      <type>  $_type  The type
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function set($_type)
	{
		return self::fav_like($_type, true);
	}


	/**
	 * unset fav or like
	 */
	public function unset($_type)
	{
		return self::fav_like($_type, false);
	}
}
?>