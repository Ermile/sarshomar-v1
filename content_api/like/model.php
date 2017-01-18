<?php
namespace content_api\like;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{
	use tools\fav_like;

	/**
	 * Posts post favorites.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_like($_args)
	{
		return $this->set("like");
	}
}
?>