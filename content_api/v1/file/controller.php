<?php
namespace content_api\v1\file;

class controller extends  \content_api\v1\home\controller
{

	/**
	 * route url like this:
	 * post > upload/ to add upload
	 * get upload/[shorturl] to get upload
	 * put upload/[shorturl] to edit upload
	 * delete upload/[shorturl] to delete upload
	 */
	public function route_file()
	{
		/**
		 * post to upload
		 */
		$this->post("upload")->ALL("v1/file");

		/**
		 * get to load upload details
		 */
		$this->get("upload")->ALL("v1/file");

		/**
		 * put to update a upload
		 */
		$this->put("upload")->ALL("v1/file");

		/**
		 * delete to delete a upload
		 */
		$this->delete("upload")->ALL("v1/file");

	}
}
?>