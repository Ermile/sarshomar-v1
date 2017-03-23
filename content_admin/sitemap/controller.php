<?php
namespace content_admin\sitemap;

class controller extends \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$this->get()->ALL();
	}
}
?>