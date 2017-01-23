<?php
namespace content_api\token\temp;

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

}
?>