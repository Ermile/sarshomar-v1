<?php
namespace content_u\filter;

class controller extends \content_u\home\controller
{
	function _route() {
		// check login
		parent::check_login();

		// add filter for survey or poll
		$this->get("filter", "filter")->ALL("/^(.*)\/filter$/");
		$this->post("filter")->ALL("/^(.*)\/filter$/");
	}
}
?>