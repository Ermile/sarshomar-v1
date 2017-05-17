<?php
namespace content\knowledge;
use \lib\saloos;

class controller extends \content\main\controller
{
	function _route()
	{
		parent::_route();

		if(\lib\router::get_url(0) == 'knowledge')
		{
			return;
		}

		$check_status = $this->access('admin:admin:view') ? false : true ;
		$load_poll =
		[
			'post_status'    => self::$accept_poll_status,
			'check_status'   => $check_status,
			'check_language' => false,
			'post_type'      => ['poll', 'survey']
		];
		if($this->model()->get_posts(false, null, $load_poll))
		{
			\lib\router::set_controller("\\content\\poll\\controller");
			return;
		}

		if(substr(\lib\router::get_url(), 0, 1) != '$')
		{
			\lib\router::set_url(trim('$/' . \lib\router::get_url(),'/'));
		}

		$property = [];
		if(\lib\storage::get('rep') == 'u')
		{
			$route_url          = "/.*/";
			$property['status'] = ["/^stop|pause|trash|publish|draft|awaiting|schedule|expired$/", true, 'status'];
		}
		else
		{
			$route_url = "/^\\$(|\/search\=([^\/]+))$/";
		}

		$property['search']   = ["/^.*$/", true, 'search'];

		$this->get("search", "search")->ALL(
		[
			'url'      => $route_url,
			'property' => $property
		]);

		$this->post("search")->ALL("/.*/");
	}
}
?>