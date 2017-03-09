<?php
namespace content_api\v1\poll\answers;
use \lib\utility;
use \lib\debug;
use \lib\utility\shortURL;

class model extends \content_api\v1\home\model
{

	use \content_api\v1\home\tools\ready;
	use \content_api\v1\poll\tools\get;

	use tools\get;
	/**
	 * Gets the options.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The options.
	 */
	public function get_poll_answers($_args = [])
	{
		return $this->get_poll_answers_list();
	}
}
?>