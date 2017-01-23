<?php
namespace content_api\price\budget\tools;
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
		if($this->user_id)
		{
			$budget = \lib\db\transactions::budget($this->user_id);
		}
		else
		{
			return null;
		}

		$result =
		[
			'gift'     => 0,
			'real'     => 0,
			'prize'    => 0,
			'transfer' => 0,
			'total'    => 0
		];

		if(is_array($budget))
		{
			$result['total'] = array_sum($budget);
			foreach ($budget as $key => $value)
			{
				if(isset($result[$key]))
				{
					$result[$key] = (float) $value;
				}
			}
		}
		return $result;
	}
}
?>