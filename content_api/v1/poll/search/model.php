<?php
namespace content_api\v1\poll\search;
use \lib\utility;

class model extends \content_api\v1\home\model
{

	public $api_mode = true;

	use tools\search;
	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The search.
	 */
	public function get_search($_args)
	{
		return $this->poll_search($_args);
	}
}
?>