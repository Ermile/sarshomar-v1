<?php 
namespace content_api\poll\tools;

trait put
{
	
	/**
	 * Puts a poll.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function put_poll($_args)
	{
		return $this->add($_args);
	}

}

?>