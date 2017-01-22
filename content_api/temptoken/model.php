<?php
namespace content_api\temptoken;

class model extends \content_api\home\model
{

	/**
	 * Gets the token.
	 *
	 * @return     <type>  The token.
	 */
	public function get_temp_token()
	{
		return $this->token();
	}


	/**
	 * Posts a token.
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_temp_token()
	{
		return $this->token();
	}

}
?>