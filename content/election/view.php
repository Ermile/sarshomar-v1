<?php
namespace content\election;

class view extends \content\home\view
{
	function config()
	{
		if($this->module() === 'home')
		{
			$this->include->js_main      = true;
		}
		else
		{
			$this->data->page['title'] = $this->data->module;
		}
	}

	public function view_election($_args)
	{
		$chart_data = $_args->api_callback;
		$this->data->chart_data = $chart_data;
	}
}
?>