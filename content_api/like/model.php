<?php 
namespace content_api\like;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{
	/**
	 * Gets the like.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The like.
	 */
	public function get_getLike($_args)
	{
		return $this->post_getlike($_args);
	}


	/**
	 * Posts a like.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_getLike($_args)
	{	
		return [];
	}


	/**
	 * Posts post like.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_addLike($_args)
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

		return \lib\db\polls::favo_like("like", $this->login('id'), utility::request('id'), $type);	
	}
}
?>