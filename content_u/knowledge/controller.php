<?php
namespace content_u\knowledge;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		parent::check_login();

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