<?php 
namespace content_api\favorites;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{
	/**
	 * Gets the favorites.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The favorites.
	 */
	public function get_getFavorites($_args)
	{
		return $this->post_getFavorites($_args);
	}


	/**
	 * Posts a favorites.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_getFavorites($_args)
	{	
		return [];
	}


	/**
	 * Posts post favorites.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_addFavorites($_args)
	{
		
		if(!$this->login("id"))
		{
			return debug::error(T_("Please login to save a favorites"), false, 'permission');
		}

		$type = null;
		if(utility::request("favorites") === '')
		{
			$type = false;
		}
		elseif(utility::request("favorites"))
		{
			$type = true;
		}

		return \lib\db\polls::favo_like("favorites", $this->login('id'), utility::request('id'), $type);	
	}
}
?>