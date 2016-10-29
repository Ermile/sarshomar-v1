<?php
namespace content_u\search;

class controller extends \content_u\home\controller
{
	function _route() {
		// check login
		parent::check_login();

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