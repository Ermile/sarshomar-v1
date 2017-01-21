<?php
namespace content_api\budget\tools;
use \lib\utility;
use \lib\debug;

trait budget
{
	/**
	 * get user budget
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function budget($_args = null)
	{
		$budget = \lib\db\transactions::budget($this->user_id);
		if(is_array($budget))
		{
			$budget['sum'] = array_sum($budget);
		}

		foreach ($budget as $key => $value)
		{
			$budget[$key] = (float) $value;
		}
		return $budget;
	}
}
?>