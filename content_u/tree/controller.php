<?php
namespace content_u\tree;

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
				"search" => ["/^.*$/", true, 'search'],
				"repository" => ["/^personal|sarshomar|all$/", true, 'repository']
			]
		]
		);
	}
}

?>