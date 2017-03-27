<?php
namespace content_admin\knowledge;

class controller extends  \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get("search", "search")->ALL(
		[
			'url' => "/.*/",
			'property' =>
			[
				"page"      => ["/^\d+$/", true, 'page'],
				"status"    => ["/^.*$/", true, 'status'],
				"search"    => ["/^.*$/", true, 'search'],
				"sarshomar" => ["/^.*$/", true, 'sarshomar'],
				"sort"      => ["/^.*$/", true, 'sort'],
				"order"     => ["/^.*$/", true, 'order'],
			]
		]
		);

		$this->post("knowledge")->ALL("/.*/");
	}
}

?>