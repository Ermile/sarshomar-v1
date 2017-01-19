<?php
namespace content_api\logintoken;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{

	/**
	 * Gets the token.
	 *
	 * @return     <type>  The token.
	 */
	public function get_login_token()
	{
		return $this->token();
	}


	/**
	 * Posts a token.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_login_token()
	{
		return $this->token();
	}

}
?>