<?php
namespace content_u\lists;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get list data to show
	 */
	public function get_list($_args)
	{

		$page = $_args->get("page");
		if($page)
		{
			$page = $page[0];
		}

		$user = $_args->get("user");
		if($user)
		{
			$user = $user[0];
		}


		$type = $_args->get("type");
		if($type)
		{
			$type = $type[0];
		}

		$filter = $_args->get("filter");
		if($filter)
		{
			$filter = $filter[0];
		}

		$value = $_args->get("value");
		if($value)
		{
			$value = $value[0];
		}

		$status = $_args->get("status");
		if($status)
		{
			$status = $status[0];
		}

		$args =
		[
			'page'        => $page,
			'user_id'     => $user,
			'post_status' => $status,
			'post_type'   => $type,
			'filter'      => $filter,
			'value'       => $value
		];

		return \lib\db\polls::xget($args);
	}


	/**
	 * post data and update or insert list data
	 */
	public function post_list()
	{

	}
}
?>