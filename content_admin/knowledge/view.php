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
		$list = $_args->api_callback;

		$this->data->poll_list = $list;
		$count_poll_status = $this->model()->count_poll_status();
		$count_poll_status['total'] = array_sum($count_poll_status);
		$this->data->poll_count = $count_poll_status;
	}

}
?>