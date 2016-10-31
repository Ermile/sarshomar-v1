<?php
namespace content_u\filter;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{

	/**
	*	get add filter
	*/
	function get_filter($_args)
	{
		// list of adds filter
		$filter_list = \lib\db\filters::get_exist_filter();
		return $filter_list;
	}


	/**
	 * save filter
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function post_filter($_args)
	{
		// get filter

		// remove empty filters in post
		$post = array_filter(utility::post());
		$filter = [];
		// get the post started by 'filter_' string
		foreach ($post as $key => $value) {
			if(preg_match("/^filter\_(.*)$/", $key, $name))
			{
				$filter[$name[1]] = $value;
			}
		}
		// very filter seleced
		if(count($filter) > 5)
		{
			debug::error(T_("oops, too many filters. remove some filter"));
			return false;
		}
		// get count member by tihs filter
		$count_filtered_member = \lib\db\filters::count_filtered_member($filter);

		// debug::warn(T_(":max members founded",["max" => $count_filtered_member]));

		// if($count_filtered_member < 1)
		// {
		// 	debug::error(T_("max = :max and this is less than 100, remove some filter",["max" => $count_filtered_member]));
		// 	return false;
		// }
		// get the poll or survey id
		$poll_id = $this->check_poll_url($_args);

		if(!$poll_id)
		{
			debug::error(T_("poll id not found"));
			return false;
		}
		// ready to insert filters in options table
		$args = [];
		foreach ($filter as $key => $value) {
			$args[] =
			[
				'post_id'      => $poll_id,
				'option_cat'   => "poll_$poll_id",
				'option_key'   => $key,
				'option_value' => $value,
				'option_meta'  => null
			];
		}
		$result = \lib\db\options::insert_multi($args);
		if(!$result)
		{
			$result = \lib\db\options::update_on_error($args);
		}

		if($result)
		{
			$short_url = $this->check_poll_url($_args, "encode");
			\lib\debug::true(T_("add filter of poll Success"));
			$this->redirector()->set_url("@/add/$short_url/publish");

		}
		else
		{
			\lib\debug::error(T_("Error in insert filter of poll"));
		}
	}
}
?>