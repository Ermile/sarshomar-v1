<?php
namespace content_api\budget;
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
		return $this->post_budget($_args);
	}

	/**
	 * Posts a budget.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_budget($_args)
	{
		return $this->budget($_args);
	}

}
?>