<?php
namespace content_u\tree;

class view extends \mvc\view
{

	/**
	 * show search result
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_search($_args)
	{
		$this->include->fontawesome = true;

		$this->data->search_value =  $_args->get("search")[0];
		// get fontawesome class
		$list = $_args->api_callback;

		$this->data->poll_list = $list;

	}

}
?>