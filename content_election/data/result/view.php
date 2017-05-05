<?php
namespace content_election\data\result;
use \lib\utility\location;

class view extends \content_election\main\view
{
	public function view_add_city($_args)
	{
		$cites                 = location\cites::list("id", 'localname');
		$provinces             = location\provinces::list("id", 'localname');
		$countres              = location\countres::list("id", 'name | localname');
		$this->data->cites     = $cites;
		$this->data->provinces = $provinces;
		$this->data->countres  = $countres;

		$result = \content_election\lib\elections::get($this->model()->getid($_args));
		$this->data->election = $result;
	}

	public function view_result($_args)
	{
		$result = $_args->api_callback;
		$this->data->result = $result;
	}

}
?>