<?php
namespace content_api\v1\token\temp;

class model extends \content_api\v1\home\model
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