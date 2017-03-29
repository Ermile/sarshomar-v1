<?php
namespace content_admin\logitems;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get log data to show
	 */
	public function get_logitems($_args)
	{
		$meta   = [];
		$search = null;
		if(isset($_args->get("search")[0]))
		{
			$search = $_args->get("search")[0];
		}

		if(isset($_args->get("sort")[0]))
		{
			$meta['sort'] = $_args->get("sort")[0];
		}

		if(isset($_args->get("order")[0]))
		{
			$meta['order'] = $_args->get("order")[0];
		}

		// if(isset($_args->get("creator")[0]))
		// {
		// 	$meta['creator'] = $_args->get("creator")[0];
		// }

		$result = \lib\db\logitems::search($search, $meta);
		return $result;
	}


	/**
	 * post data and update or insert log data
	 */
	public function post_logitems()
	{

	}
}
?>