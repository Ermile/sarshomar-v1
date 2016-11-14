<?php
namespace content_admin\knowledge;

class view extends \mvc\view
{

	/**
	 * show search result
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_search($_args)
	{
		$this->data->search_value =  $_args->get("search")[0];
		// get fontawesome class
		$list = $_args->api_callback;
		if($list && is_array($list))
		{
			foreach ($list as $key => $value)
			{
				$list[$key]['type'] = \content_u\knowledge\view::find_icon($value['type']);
			}
		}
		$this->data->poll_list = $list;
	}

}
?>