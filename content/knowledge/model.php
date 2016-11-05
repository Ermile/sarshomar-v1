<?php
namespace content\knowledge;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * search in polls
	 */
	// public function post_search()
	// {
	// 	$search = utility::post("search");
	// 	$result = \lib\db\polls::search($search);
	// 	return $result;
	// }


	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_search($_args)
	{
		$match = $_args;
		unset($_args->match->url);
		unset($_args->method);
		unset($_args->match->property);
		$match  = $match->match;

		$filter = [];

		foreach ($match as $key => $value) {
			if(is_array($value) && isset($value[0]))
			{
				$value = $value[0];
			}
			if(\lib\db\filters::support_filter($key))
			{
				$filter[$key] = $value;
			}
		}

		$filter_id = [];
		if(!empty($filter))
		{
			$filter_id = \lib\db\filters::get_id($filter);
			$filter_id = ['filter_id' => $filter_id];
		}

		$search = $_args->get("search")[0];
		$result = \lib\db\polls::search($search, $filter_id);
		return $result;
	}
}
?>