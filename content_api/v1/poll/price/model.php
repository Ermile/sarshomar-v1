<?php
namespace content_api\v1\poll\price;
use \lib\utility;

class model extends \content_api\v1\home\model
{

	use tools\price;
	/**
	 * Gets the price.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The price.
	 */
	public function get_price($_args)
	{
		return $this->poll_price($_args);
	}
}
?>