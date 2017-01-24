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
		$this->post("upload")->ALL("file");

		/**
		 * get to load upload details
		 */
		$this->get("upload")->ALL("file");

		/**
		 * put to update a upload
		 */
		$this->put("upload")->ALL("file");

		/**
		 * delete to delete a upload
		 */
		$this->delete("upload")->ALL("file");

	}
}
?>