<?php
namespace content_election\admin\election;

class view extends \mvc\view
{
	public function view_election($_args)
	{
		$this->data->edit_election = true;
		$result = $_args->api_callback;
		$this->data->election = $result;
	}


	public function view_list($_args)
	{
		$this->data->election_list = $_args->api_callback;

	}
}
?>