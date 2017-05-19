<?php
namespace content_election\home;

class view extends \content_election\main\view
{

	/**
	 * { function_description }
	 */
	public function config()
	{
		$running  = [];
		$election = \content_election\lib\elections::search();

		$this->data->election_list = $election;

		foreach ($election as $key => $value)
		{
			if(isset($value['status']) && $value['status'] === 'running')
			{
				$running[] = $value;
			}
		}

		$this->data->running = $running;

		if($this->access('election:admin:admin'))
		{
			$this->data->perm_admin = true;
		}

		if($this->access('election:data:admin'))
		{
			$this->data->perm_data = true;
		}
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


	/**
	 * view candida
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_home($_args)
	{
		$this->data->result = $_args->api_callback;
	}


	/**
	 * view candida
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_candida($_args)
	{
		$this->data->result = $_args->api_callback;
	}


	public function view_comment($_args)
	{

	}
}
?>