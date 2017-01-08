<?php
namespace content_api\file;

class controller extends  \mvc\controller
{	
	public function __construct()
	{
		\lib\storage::set_api(true);
		parent::__construct();
	}


	/**
	 * route url like this:
	 * post > upload/ to add upload
	 * get upload/[shorturl] to get upload
	 * put upload/[shorturl] to edit upload
	 * delete upload/[shorturl] to delete upload
	 */
	public function _route()
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