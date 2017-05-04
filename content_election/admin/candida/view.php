<?php
namespace content_election\admin\candida;

class view extends \content_election\main\view
{
	public function config()
	{
		$this->data->election_list = \content_election\lib\elections::search();
	}

	public function view_candida($_args)
	{
		$this->data->edit_candida = true;
		$result = $_args->api_callback;
		$this->data->candida = $result;
	}


	public function view_list($_args)
	{
		$this->data->candida_list = $_args->api_callback;

	}
}
?>