<?php
namespace content_api\v1\profile;

class model extends \content_api\v1\home\model
{
	use tools\get;
	use tools\set;

	/**
	 * Links an upload.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function get_profile($_args)
	{
		return $this->get_user_profile();
	}


	/**
	 * Gets the upload.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The upload.
	 */
	public function post_profile($_args)
	{
		return $this->set_user_profile();
	}
}
?>