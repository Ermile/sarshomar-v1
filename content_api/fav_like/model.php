<?php 
namespace content_api\fav_like;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{
	/**
	 * set a fav or like
	 */
	public function set($_type)
	{
		if(!$this->login("id"))
		{
			return debug::error(T_("Please login to save a :type", ['type' => $_type]), false, 'permission');
		}

		$type = null;
		if(utility::request("fav") === false)
		{
			$type = false;
		}
		elseif(utility::request("fav"))
		{
			$type = true;
		}

		if(!preg_match("/^[". \lib\utility\shortURL::ALPHABET. "]+$/", utility::request("id")))
		{
			return \lib\debug::error(T_("Invalid parametr id"), 'id', 'arguments');
		}

		$poll_id = \lib\utility\shortURL::decode(utility::request('id'));
		return \lib\db\polls::$_type($this->login('id'), $poll_id, ['set_or_unset' => $type]);
	}


	/**
	 * Posts post favorites.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_favorites($_args)
	{
		return $this->set("fav");
	}


	/**
	 * Posts post favorites.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_like($_args)
	{
		return $this->set("like");
	}
}
?>