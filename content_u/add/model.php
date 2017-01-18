<?php
namespace content_u\add;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{
	use filter\model;
	use publish\model;

	public function get_edit($_args)
	{

	}


	public function post_add($_args)
	{
		if($this->term_list())
		{
			return;
		}
	}

	public function post_edit($_args)
	{
		if($this->term_list())
		{
			return;
		}
	}


	use \content_api\tag\tools\search;

	private function term_list()
	{
		if(utility::post("list"))
		{
			utility::$REQUEST = new utility\request(
				[
					'method' => 'array',
					'request' =>
					[
						'type'   => utility::post("list"),
						'search' => utility::post("q")
					]
				]
			);
			$result = $this->search();
			debug::msg("list", $result);
			return true;
		}
		return false;
	}

}
?>