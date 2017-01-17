<?php 
namespace content_api\tag;
use \lib\utility;

class model extends \content_api\home\model
{

	use tools\search;
	/**
	 * Gets the tag.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The tag.
	 */
	public function get_tag($_args)
	{
		return $this->post_tag($_args);
	}

	/**
	 * Posts a tag.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_tag($_args)
	{	
		return $this->search($_args);
	}

}
?>