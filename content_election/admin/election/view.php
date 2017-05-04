<?php
namespace content_election\admin\election;

class view extends \content_election\main\view
{
	public function view_election($_args)
	{
		$this->data->edit_election = true;
		$result = $_args->api_callback;
		$this->data->election = $result;
		$this->data->candidas = \content_election\lib\candidas::search(null,['election_id' => $this->model()->getid($_args)]);

	}


	public function view_list($_args)
	{
		$this->data->election_list = $_args->api_callback;
		$this->data->candidas = \content_election\lib\candidas::search();

	}
}
?>