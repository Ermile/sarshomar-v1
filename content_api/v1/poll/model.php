<?php
namespace content_api\v1\poll;
use \lib\utility;

class model extends \content_api\v1\home\model
{
	use tools\add;
	use tools\delete;
	use tools\get;

	/**
	 * delete a poll
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function delete_poll($_args)
	{
		return $this->poll_delete(['id' => \lib\router::get_url(2)]);
	}


	/**
	 * Gets the poll.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The poll.
	 */
	public function get_poll($_args)
	{
		return $this->poll_get();
	}


	/**
	 * Posts a poll.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_poll($_args)
	{
		return $this->poll_add(['args' => $_args]);
	}


	/**
	 * Puts a poll.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function put_poll($_args)
	{
		return $this->poll_add(['args' => $_args, 'method' => 'put']);
	}


	/**
	 * patch a poll
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function patch_poll($_args)
	{
		return $this->poll_add(['args' => $_args, 'method' => 'patch']);
	}


	/**
	 * Gets the ask.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The ask.
	 */
	public function get_ask($_args)
	{
		return $this->poll_get(['type' => 'ask']);
	}


	/**
	 * Gets the random.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The random.
	 */
	public function get_random($_args)
	{
		return $this->poll_get(['type' => 'random']);
	}

}
?>