<?php
namespace content_election\data\result;
use \lib\utility;
use \lib\debug;

class model extends \content_election\main\model
{
	public function get_result($_args)
	{
		$id = $this->getid($_args);
		if($id)
		{
			$result = \content_election\lib\results::search(null, ['election_id' => $id]);
			return $result;
		}
	}
}
?>