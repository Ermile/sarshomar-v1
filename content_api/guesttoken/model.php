<?php
namespace content_api\guesttoken;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{
	/**
	 * Gets the guest token.
	 *
	 * @return     <type>  The guest token.
	 */
	public function get_guest_token()
	{
		return $this->token(true);
	}


	/**
	 * Posts a guest token.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_guest_token()
	{
		return $this->token(true);
	}
}
?>