<?php
namespace content_admin\knowledge;
use \lib\utility;

class model extends \mvc\model
{

	public function post_knowledge()
	{
		return \lib\db\polls::search(utility::post('search'));
	}

	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_search($_args)
	{
		if(isset($_args->match->url[0][0]) && $_args->match->url[0][0] == '')
		{
			return \lib\db\polls::get_last_poll(['limit' => 10]);
		}

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

		$meta                   = [];
		$meta['post_sarshomar'] = 1;
		if(!empty($filter))
		{
			$filter_id         = \lib\db\filters::get_id($filter);
			$meta['filter_id'] = $filter_id;
		}
		$search = $_args->get("search")[0];
		$result = \lib\db\polls::search($search, $meta);
		return $result;
	}

}
?>