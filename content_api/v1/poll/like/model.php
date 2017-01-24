<?php
namespace content_api\v1\poll\like;
use \lib\utility;
use \lib\debug;

class model extends \content_api\v1\home\model
{
	use \content_api\v1\poll\tools\fav_like;

	/**
	 * Posts post favorites.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_like($_args)
	{
		return $this->set("like");
	}

	public function delete_like($_args)
	{
		return $this->unset("like");
	}

}
?>