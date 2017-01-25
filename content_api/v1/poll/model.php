<?php
namespace content_api\v1\poll;
use \lib\utility;

class model extends \content_api\v1\home\model
{
	use tools\add;
	use tools\get;

	/**
	 * delete a poll
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function delete_poll($_args)
	{
		return "delete";
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
		return $this->get();
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
		return $this->add($_args);
	}


	/**
	 * Puts a poll.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function put_poll($_args)
	{
		return $this->add($_args, true);
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
		return $this->get(['ask' => true]);
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
		return $this->get(['random' => true]);
	}

}
?>