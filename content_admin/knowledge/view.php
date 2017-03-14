<?php
namespace content_admin\knowledge;

class view extends \mvc\view
{

	public function config()
	{
		// add all template of knowledge into new file
		$this->data->template['knowledge']['layout'] = 'content/knowledge/layout.html';
		$this->data->display['result'] = "content/knowledge/layout-xhr.html";
		$this->data->xlarge = true;
		$this->data->admin  = true;
	}

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


	/**
	 * [pushState description]
	 * @return [type] [description]
	 */
	function pushState()
	{
		if(\lib\utility::get('onlySearch'))
		{
			$this->data->display['main'] = "content/knowledge/layout-xhr.html";
		}
	}

}
?>