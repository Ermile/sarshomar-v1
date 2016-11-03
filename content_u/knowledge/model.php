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
		$result = \lib\db\polls::me_search($user_id, $search);
		return $result;

	}


	/**
	 * post data and update or insert list data
	 */
	public function post_knowledge()
	{
		if(utility::post("poll_id"))
		{
			$arg = ['post_status' => 'publish'];
			$update = \lib\db\polls::update($arg, utility::post("poll_id"));
			if($update)
			{
				\lib\debug::true(T_("poll publish"));
			}
			else
			{
				\lib\debug::error(T_("can not publish poll"));
			}
		}
	}
}
?>