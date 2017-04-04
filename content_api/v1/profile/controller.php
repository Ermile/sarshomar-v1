<?php
namespace content_api\v1\profile;

class controller extends  \content_api\v1\home\controller
{

	public function _route()
	{
		/**
		 * get to profile
		 */
		// $this->post("profile")->ALL("v1/profile");

		/**
		 * get to profile
		 */
		// $this->put("profile")->ALL("v1/profile");

		/**
		 * get to profile
		 */
		$this->patch("profile")->ALL("v1/profile");

		/**
		 * get to load profile details
		 */
		$this->get("profile")->ALL("v1/profile");

	}
}
?>