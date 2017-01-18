<?php
namespace content_api\fav;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{
	use \content_api\like\tools\fav_like;

	/**
	 * Posts post favorites.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_favorites($_args)
	{
		return $this->set("fav");
	}

}
?>