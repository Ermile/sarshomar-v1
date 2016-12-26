<?php
namespace content_admin\knowledge;

class controller extends  \content_admin\home\controller
{
	public function _route()
	{
		parent::check_login();


		$this->get("search", "search")->ALL(
		[
			'url' => "/.*/",
			'property' =>
			[
				"search" => ["/^(.*)$/", true, 'search'],
				"page" => ["/^\d+$/", true, 'page']
			]
		]
		);

		$this->post("knowledge")->ALL("/.*/");
	}
}

?>