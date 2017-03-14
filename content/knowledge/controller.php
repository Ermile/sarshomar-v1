<?php
namespace content\knowledge;
use \lib\saloos;

class controller extends \mvc\controller
{
	function _route()
	{
		if(\lib\router::get_url(0) == 'knowledge')
		{
			return;
		}

		$check_status = $this->access('admin','admin', 'view') ? false : true ;

		if($this->model()->get_posts(false, null, ['check_status' => $check_status, 'check_language' => false, 'post_type' => ['poll', 'survey']]))
		{
			\lib\router::set_controller("\\content\\poll\\controller");
			return;
		}

		if(substr(\lib\router::get_url(), 0, 1) != '$')
		{
			\lib\router::set_url(trim('$/' . \lib\router::get_url(),'/'));
		}

		$property               = [];
		$property['search']     = ["/^.*$/", true, 'search'];
		$this->get("search", "search")->ALL(
		[
			'url'      => "/^\\$(|\/search\=([^\/]+))$/",
			'property' => $property
		]);

		$this->post("search")->ALL("/.*/");
	}
}
?>