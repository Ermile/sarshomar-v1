<?php
namespace content_api\price\budget;
use \lib\utility;

class model extends \content_api\home\model
{

	use tools\budget;
	/**
	 * Gets the budget.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The budget.
	 */
	public function get_budget($_args)
	{
		return $this->budget($_args);
	}
}
?>