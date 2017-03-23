<?php
namespace content_u\search;

class controller extends \content_u\main\controller
{
	public function _route()
	{
		parent::_route();

		// search
		$this->get("search", "search")->ALL(
		[
			'property' =>
			[
				"title" => ["/^(.*)$/", true, 'title'],
				"type"  => ["/^(.*)$/", true, 'type']
			]
		]
		);
	}
}
?>