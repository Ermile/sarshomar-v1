<?php
namespace content_api\poll;

class controller extends  \content_api\home\controller
{

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
		$this->post("poll")->ALL("poll");

		/**
		 * get to load poll details
		 */
		$this->get("poll")->ALL("poll");

		/**
		 * put to update a poll
		 */
		$this->put("poll")->ALL("poll");

		/**
		 * delete to delete a poll
		 */
		$this->delete("poll")->ALL("poll");

	}
}
?>