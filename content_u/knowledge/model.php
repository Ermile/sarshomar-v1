<?php
namespace content_u\knowledge;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_search($_args)
	{
		if(isset($_args->match->url[0][0]) && $_args->match->url[0][0] == '$')
		{
			return \lib\db\polls::search(null,
				[
					'my_poll'  => true,
					'get_last' => true,
					'user_id'  => $this->login('id')
				]);
		}

		// $match = $_args;
		// unset($_args->match->url);
		// unset($_args->method);
		// unset($_args->match->property);
		// $match  = $match->match;

		// $filter = [];

		// foreach ($match as $key => $value) {
		// 	if(is_array($value) && isset($value[0]))
		// 	{
		// 		$value = $value[0];
		// 	}
		// 	if(\lib\db\filters::support_filter($key))
		// 	{
		// 		$filter[$key] = $value;
		// 	}
		// }

		// if(!empty($filter))
		// {
		// 	$filter_id         = \lib\db\filters::get_id($filter);
		// 	$meta['filter_id'] = $filter_id;
		// }

		$meta            = [];
		$meta['user_id'] = $this->login('id');
		$meta['my_poll'] = true;
		$search = $_args->get("search")[0];
		$result = \lib\db\polls::search($search, $meta);
		return $result;
	}

}
?>