<?php
namespace content_election\main;

class model extends \mvc\model
{
	/**
	 * Gets the identifier from url
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  The identifier.
	 */
	public function getid($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : false;
		if(!$id)
		{
			return false;
		}
		return $id;
	}
}
?>