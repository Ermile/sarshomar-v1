<?php
namespace content\knowledge;
use \lib\utility;

class model extends \mvc\model
{

	public function post_search()
	{

		if(utility::post("type") == 'faivorites')
		{
			if($this->login())
			{
				\lib\db\polls::faiv_like("faivorites", $this->login('id'), utility::post('id'));
			}
			return;
		}
		$field = [];
		if($this->login())
		{
			// to get faivorites posts
			$field = ['login' => $this->login('id')];
		}

		return \lib\db\polls::search(utility::post("search"), $field);
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
			$field = [];
			$field['limit'] = 10;
			if($this->login())
			{
				$field['login'] = $this->login('id');
			}
			return \lib\db\polls::get_last_poll($field);
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