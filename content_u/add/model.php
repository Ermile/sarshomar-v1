<?php
namespace content_u\add;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{
	use filter\model;
	use publish\model;

	public function get_add($_args)
	{
		if($this->term_list())
		{
			return;
		}
	}


	public function get_edit($_args)
	{
		if($this->term_list())
		{
			return;
		}
	}


	public function post_add($_args)
	{
		if($this->term_list())
		{
			return;
		}

		return $this->poll();
	}

	public function post_edit($_args)
	{
		if($this->term_list())
		{
			return;
		}

		return $this->poll($_args);
	}


	use \content_api\v1\poll\tools\add;

	public function poll($_args = null)
	{
		$id = null;
		if($_args)
		{
			$id = (isset($_args->get("url")[0][1])) ? $_args->get("url")[0][1] : null;
		}

		$request 							= [];
		$request['title'] 					= utility::post("title");
		$request['type'] 					= utility::post("type");
		$request['options'] 				= [];
		$request['options']['random_sort'] 	= utility::post("random_sort");

		if($id)
		{
			$request['id'] = $id;
		}

		utility::$REQUEST = new utility\request(
			[
				'method' => 'array',
				'request' => $request
			]
			);

		$this->user_id = $this->login('id');

		return $this->add(null, $id);

	}

	use \content_api\v1\tag\search\tools\search;

	private function term_list()
	{
		if(utility::get("list"))
		{
			utility::$REQUEST = new utility\request(
				[
					'method' => 'array',
					'request' =>
					[
						'type'   => utility::get("list"),
						'search' => utility::get("q")
					]
				]
			);
			$result = $this->search();
			$result = json_encode($result);
			debug::msg("list", $result);
			return true;
		}
		return false;
	}

}
?>