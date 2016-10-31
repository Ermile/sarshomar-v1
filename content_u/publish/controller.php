<?php
namespace content_u\publish;

class controller extends \content_u\home\controller
{
	function _route() {
		// check login
		parent::check_login();

		// publish survey or poll
		$this->get("publish", "publish")->ALL("/^add\/(.*)\/publish$/");
		$this->post("publish")->ALL("/^add\/(.*)\/publish$/");
	}
}
?>