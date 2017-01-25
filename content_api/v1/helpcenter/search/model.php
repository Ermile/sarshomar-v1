<?php
namespace content_api\v1\helpcenter\search;
use \lib\utility;

class model extends \content_api\v1\home\model
{

	use tools\search;

	/**
	 * Gets the helpcenter.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The helpcenter.
	 */
	public function get_helpcenter($_args)
	{
		return $this->search($_args);
	}

	/**
	 * Posts a helpcenter.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_helpcenter($_args)
	{
		// return $this->add($_args);
	}

}
?>