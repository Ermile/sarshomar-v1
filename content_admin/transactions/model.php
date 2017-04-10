<?php
namespace content_admin\transactions;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get log data to show
	 */
	public function get_transactions($_args)
	{
		$meta   = [];
		$search = null;
		if(isset($_args->get("search")[0]))
		{
			$search = $_args->get("search")[0];
		}

		if(isset($_args->get("order")[0]))
		{
			$meta['order'] = $_args->get("order")[0];
		}

		if(isset($_args->get("sort")[0]))
		{
			$meta['sort'] = $_args->get("sort")[0];
		}

		if(isset($_args->get("type")[0]))
		{
			$meta['type'] = $_args->get("type")[0];
		}

		if(isset($_args->get("caller")[0]))
		{
			$meta['caller'] = $_args->get("caller")[0];
		}

		if(isset($_args->get("user")[0]))
		{
			$meta['user'] = $_args->get("user")[0];
		}

		if(isset($_args->get("date")[0]))
		{
			$meta['date'] = $_args->get("date")[0];
		}

		if(isset($_args->get("time")[0]))
		{
			$meta['time'] = $_args->get("time")[0];
		}
		$meta['admin'] = true;
		$result = \lib\db\transactions::search($search, $meta);
		return $result;
	}


	/**
	 * post data and update or insert log data
	 */
	public function post_transactions()
	{

	}
}
?>