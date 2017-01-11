<?php 
namespace content_api\favorites;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{

	/**
	 * Posts post favorites.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_favorites($_args)
	{
		
		if(!$this->login("id"))
		{
			return debug::error(T_("Please login to save a favorites"), false, 'permission');
		}

		$type = null;
		if(utility::request("fav") === '')
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
		return \lib\db\polls::fav($this->login('id'), $poll_id, $type);	
	}
}
?>