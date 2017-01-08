<?php
namespace content_api\poll;

class controller extends  \mvc\controller
{	
	public function __construct()
	{
		\lib\storage::set_api(true);
		parent::__construct();
	}

	/**
	 * the short url
	 *
	 * @var        string
	 */
	public static $shortURL = "23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ";
	

	/**
	 * route url like this:
	 * post > poll/ to add poll
	 * get poll/[shorturl] to get poll
	 * put poll/[shorturl] to edit poll
	 * delete poll/[shorturl] to delete poll
	 */
	public function _route()
	{
		/**
		 * post to add a poll
		 */
		$this->post("poll")->REST("/^addPoll$/");
		
		/**
		 * get to load poll details
		 */
		$this->get("poll")->REST("/^getPoll$/");
			
		/**
		 * put to update a poll
		 */
		$this->put("poll")->REST("/^editPoll$/");
			
		/**
		 * delete to delete a poll
		 */
		$this->delete("poll")->REST("/^deletePoll$/");

	}
}
?>