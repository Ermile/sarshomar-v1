<?php
namespace content_api\file;

trait controller
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
		$this->post("upload")->REST("/^upload$/");

		/**
		 * get to load upload details
		 */
		$this->get("upload")->REST("/^upload\/([". self::$shortURL. "]+)$/");

		/**
		 * put to update a upload
		 */
		$this->put("upload")->REST("/^upload\/([". self::$shortURL. "]+)$/");

		/**
		 * delete to delete a upload
		 */
		$this->delete("upload")->REST("/^upload\/([". self::$shortURL. "]+)$/");

	}
}
?>