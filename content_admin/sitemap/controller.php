<?php
namespace content_admin\sitemap;

class controller extends \content_admin\home\controller
{
	public function _route()
	{
		parent::check_login();

		$this->get()->ALL();
	}
}
?>