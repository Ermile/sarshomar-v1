<?php
namespace content_election\data;

class view extends \content_election\main\view
{
	/**
	 * cofnig
	 */
	public function config()
	{
		$election = \content_election\lib\elections::search(null, ['limit' => 50]);

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
}
?>