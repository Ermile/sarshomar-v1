<?php
namespace content_u\knowledge;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get list data to show
	 */
	public function get_search($_args)
	{
		$user_id = $this->login("id");
		$search = $_args->get("search");
		if($search)
		{
			$search = $search[0];
		}
		$result = \lib\db\polls::search($search, ['user_id' => $user_id]);
		return $result;
	}

}
?>