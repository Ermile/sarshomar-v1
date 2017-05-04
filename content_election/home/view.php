<?php
namespace content_election\home;

class view extends \content_election\main\view
{

	/**
	 * { function_description }
	 */
	public function config()
	{
		$election = \content_election\lib\elections::search();
		$this->data->election_list = $election;
		$running = [];
		foreach ($election as $key => $value)
		{
			if(isset($value['status']) && $value['status'] === 'running')
			{
				$running[] = $value;
			}
		}

		$this->data->running = $running;
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_load($_args)
	{
		$result = $_args->api_callback;
		$this->data->result = $result;
	}
}
?>