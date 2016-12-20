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
		$this->include->fontawesome = true;
		$list = $_args->api_callback;

		$this->data->poll_list = $list;
	}

}
?>