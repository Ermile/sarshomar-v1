<?php
namespace content_api\v1\token\login;
use \lib\utility;
use \lib\debug;

class model extends \content_api\v1\home\model
{

	/**
	 * Gets the token.
	 *
	 * @return     <type>  The token.
	 */
	public function get_login_token()
	{
		return $this->check_verify();
	}
}
?>