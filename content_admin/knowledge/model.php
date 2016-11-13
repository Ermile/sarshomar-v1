<?php
namespace content_admin\knowledge;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get list data to show
	 */
	public function get_search($_args)
	{
		$search = $_args->get("search");
		if($search)
		{
			$search = $search[0];
		}
		$result = \lib\db\polls::search($search);
		if(!$result)
		{
			$result = [];
		}
		return $result;
	}

}
?>