<?php
namespace content_api\poll\fav;
use \lib\utility;
use \lib\debug;

class model extends \content_api\home\model
{
	use \content_api\poll\tools\fav_like;

	/**
	 * Posts post favorites.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_favorites($_args)
	{
		return $this->set("fav");
	}

	public function delete_favorites($_args)
	{
		return $this->unset("fav");
	}

}
?>