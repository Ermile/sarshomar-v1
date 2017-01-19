<?php
namespace content_api\like\tools;
use \lib\debug;
use \lib\utility;

trait fav_like
{
	public function set($_type)
	{

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
		return \lib\db\polls::$_type($this->user_id, $poll_id, ['set_or_unset' => $type]);
	}
}
?>