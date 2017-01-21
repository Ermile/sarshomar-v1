<?php
namespace content_api\price;
use \lib\utility;

class model extends \content_api\home\model
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
		return $this->post_price($_args);
	}

	/**
	 * Posts a price.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_price($_args)
	{
		return $this->price($_args);
	}

}
?>