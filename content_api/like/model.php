<?php 
namespace content_api\like;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{
	
	/**
	 * Posts post like.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_like($_args)
	{
		
		if(!$this->login("id"))
		{
			return debug::error(T_("Please login to save a like"), false, 'permission');
		}

		$type = null;
		if(utility::request("like") === '')
		{
			$type = false;
		}
		elseif(utility::request("like"))
		{
			$type = true;
		}

		if(!preg_match("/^[". \lib\utility\shortURL::ALPHABET. "]+$/", utility::request("id")))
		{
			return \lib\debug::error(T_("Invalid parametr id"), 'id', 'arguments');
		}

		$poll_id = \lib\utility\shortURL::decode(utility::request('id'));

		return \lib\db\polls::like($this->login('id'), $poll_id, $type);	
	}
}
?>