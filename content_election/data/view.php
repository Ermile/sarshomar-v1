<?php
namespace content_election\data;

class view extends \content_election\main\view
{
	public function config()
	{
		$this->data->election_list = \content_election\lib\elections::search();
	}
}
?>