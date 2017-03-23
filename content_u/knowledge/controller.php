<?php
namespace content_u\knowledge;

class controller extends  \content_u\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get("search", "search")->ALL(
		[
			'url' => "/.*/",
			'property' =>
			[
				"search" => ["/^(.*)$/", true, 'search']
			]
		]
		);
	}
}

?>