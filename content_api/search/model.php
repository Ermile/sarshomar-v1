<?php 
namespace content_api\search;
use \lib\utility;

class model extends \content_api\home\model
{

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
		return $this->post_search($_args);
	}

	/**
	 * Posts a search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_search($_args)
	{	
		return $this->search($_args);
	}

}
?>