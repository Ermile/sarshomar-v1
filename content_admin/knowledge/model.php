<?php
namespace content_admin\knowledge;
use \lib\utility;

class model extends \mvc\model
{

	public function post_knowledge()
	{
		if(utility::post('spam-word'))
		{
			\lib\db\words::set_status(utility::post('spam-word'), utility::post("status"));
			\lib\debug::true(T_("words change status"));
			return;
		}

		$id     = utility::post("id");
		$status = utility::post("status");

		if($status == 'ok')
		{
			$update = ['post_status' => 'publish'];
			// dave start date and end date in post_meta
			$update_post_meta = \lib\db\polls::merge_meta(['review' => 'ok'], $id);
		}
		else
		{
			$update = ['post_status' => $status];
			$update_post_meta = \lib\db\polls::remove_index_meta(['review' => 'ok'], $id);
		}
		$result = \lib\db\polls::update($update, $id);
		if($result)
		{
			\lib\debug::true(T_("post status update"));
		}
		else
		{
			\lib\debug::error(T_("error in update post"));
		}

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