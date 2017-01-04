<?php 
namespace content_api\poll\tools;

trait post 
{
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

}

?>