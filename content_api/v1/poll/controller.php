<?php
namespace content_api\v1\poll;

class controller extends  \content_api\v1\home\controller
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
		$this->post("poll")->ALL("v1/poll");

		/**
		 * get to load poll details
		 */
		$this->get("poll")->ALL("v1/poll");

		/**
		 * get ask poll
		 */
		$this->get("ask")->ALL("v1/poll/ask");

		/**
		 * get random poll
		 */
		$this->get("random")->ALL("v1/poll/random");

		/**
		 * put to update a poll
		 */
		$this->put("poll")->ALL("v1/poll");

		/**
		 * patch to update a poll
		 */
		$this->patch("poll")->ALL("v1/poll");

		/**
		 * delete to delete a poll
		 */
		$this->delete("poll")->ALL("/^v1\/poll\/[". \lib\utility\shortURL::ALPHABET ."]+$/");
	}
}
?>