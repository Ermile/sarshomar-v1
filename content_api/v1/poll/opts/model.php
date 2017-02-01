<?php
namespace content_api\v1\poll\opts;
use \lib\utility;
use \lib\debug;

class model extends \content_api\v1\home\model
{

	use \content_api\v1\poll\tools\get;

	use \content_api\v1\home\tools\ready;

	use \content_api\v1\poll\opts\tools\get;


	/**
	 * Gets the options.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The options.
	 */
	public function get_opts($_args)
	{
		return $this->poll_opts();
	}

}
?>