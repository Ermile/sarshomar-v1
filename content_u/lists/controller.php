<?php
namespace content_u\lists;

class controller extends  \content_u\home\controller
{
	public function _route()
	{
		parent::check_login();

		// $this->post("last")->ALL();
		$this->get("list", "list")->ALL(
		[
			'property' =>
			[
				"user"   => ["/^\d+$/", true, 'user'],
				"page"   => ["/^\d+$/", true, 'page'],
				"type"   => ["/^(.*)$/", true, 'type'],
				"status" => ["/^(.*)$/", true, 'status'],
				"filter" => ["/^(.*)$/", true, 'filter'],
				"value" => ["/^(.*)$/", true, 'value'],
				"q"      => ["/^(.*)$/", true, 'search']
			]
		]);

		$this->post("list")->ALL();
	}
}

?>