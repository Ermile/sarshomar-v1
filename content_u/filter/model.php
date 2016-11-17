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

		// get the poll or survey id
		$poll_id = $this->check_poll_url($_args);

		if(!$poll_id)
		{
			debug::error(T_("poll id not found"));
			return false;
		}

		$post = array_filter(utility::post());
		$filter = [];
		foreach ($post as $key => $value)
		{
			if(substr($key, 0, 7) == 'filter_')
			{
				$key = str_replace('filter_', '', $key);
				$value = explode(',', $value);
				$value = array_filter($value);
				if(count($value) == 1)
				{
					$filter[$key] = $value[0];
				}
				else
				{
					foreach ($value as $k => $v) {
						$filter[$key][] = $v;
					}
				}
			}
		}
		$count = \lib\db\filters::count_user($filter);

		if(intval($count) < 1)
		{
			debug::error(T_(":max users found remove some filter",["max" => $count]));
			return false;
		}

		// ready to insert filters in options table
		// get the filter id if exist
		$filter_id = \lib\db\filters::get_id($filter);

		// if filter id not found insert the filter record and get the last_insert_id
		if(!$filter_id)
		{
			$filter_id = \lib\db\filters::insert($filter);
		}

		if($filter_id)
		{
			$poll_update = ['filter_id' => $filter_id];
			$result      = \lib\db\polls::update($poll_update, $poll_id);
		}

		if($result || empty($filter))
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