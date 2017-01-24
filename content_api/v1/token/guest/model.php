<?php
namespace content_api\v1\token\guest;
use \lib\utility;
use \lib\debug;

class model extends \content_api\v1\home\model
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

}
?>