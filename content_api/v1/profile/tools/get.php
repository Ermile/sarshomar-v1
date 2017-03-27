<?php
namespace content_api\v1\profile\tools;

trait get
{
	/**
	 * Gets the upload.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     array   The upload.
	 */
	public function get_user_profile($_options = [])
	{
		if(!$this->user_id)
		{
			return;
		}

		$profile_data = \lib\utility\profiles::get_profile_data($this->user_id);

		return $profile_data;
	}
}
?>