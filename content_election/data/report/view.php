<?php
namespace content_election\data\report;

class view extends \content_election\main\view
{
	public function config()
	{
		$this->data->election_list = \content_election\lib\elections::search();
	}

	public function view_report($_args)
	{
		$this->data->edit_report = true;
		$result = $_args->api_callback;
		$this->data->report = $result;
	}


	public function view_add($_args)
	{
		$this->data->list = $_args->api_callback;
	}


	public function view_list($_args)
	{
		$this->data->report_list = $_args->api_callback;

	}
}
?>