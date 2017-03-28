<?php
namespace content_api\v1\profile;

class model extends \content_api\v1\home\model
{
	use tools\get;
	use tools\set;

	/**
	 * Gets the profile.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The profile.
	 */
	public function get_profile($_args)
	{
		return $this->get_user_profile();
	}


	/**
	 * Posts a profile.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_profile($_args)
	{
		return $this->set_user_profile(['method' => 'post']);
	}


	/**
	 * Puts a profile.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function put_profile($_args)
	{
		return $this->set_user_profile(['method' => 'put']);
	}


	/**
	 * patch the profile
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function patch_profile($_args)
	{
		return $this->set_user_profile(['method' => 'patch']);
	}
}
?>