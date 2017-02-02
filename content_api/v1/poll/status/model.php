<?php
namespace content_api\v1\poll\status;
use \lib\utility;
use \lib\debug;

class model extends \content_api\v1\home\model
{

	use \content_api\v1\poll\tools\get;

	use \content_api\v1\home\tools\ready;

	use \content_api\v1\poll\status\tools\get;

	use \content_api\v1\poll\status\tools\set;


	/**
	 * Gets the options.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The options.
	 */
	public function get_status($_args)
	{
		return $this->poll_status();
	}


	/**
	 * Puts a status.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function put_status($_args)
	{
		return $this->poll_set_status();
	}

}
?>