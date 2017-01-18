<?php
namespace content_api\poll;

trait controller
{

	/**
	 * route url like this:
	 * post > poll/ to add poll
	 * get poll/[shorturl] to get poll
	 * put poll/[shorturl] to edit poll
	 * delete poll/[shorturl] to delete poll
	 */
	public function route_poll()
	{
		/**
		 * post to add a poll
		 */
		$this->post("poll")->ALL("addPoll");

		/**
		 * get to load poll details
		 */
		$this->get("poll")->ALL("getPoll");
		$this->post("getPoll")->ALL("getPoll");

		/**
		 * put to update a poll
		 */
		$this->put("poll")->ALL("editPoll");

		/**
		 * delete to delete a poll
		 */
		$this->delete("poll")->ALL("deletePoll");

	}
}
?>