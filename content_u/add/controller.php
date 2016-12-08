<?php
namespace content_u\add;

class controller extends \content_u\home\controller
{
	function _route()
	{

		// check login
		parent::check_login();

		if(preg_match("/(filter|publish)$/", \lib\router::get_url(), $load))
		{
			if(isset($load[1]))
			{
				$this->model_name   = "\\content_u\\add\\$load[1]\\model";
				$this->view_name    = "\\content_u\\add\\$load[1]\\view";
				$this->display_name = "\\content_u\\add\\$load[1]\\display.html";

				$this->get("filter", "filter")->ALL("/^add\/([^\/]*)\/filter$/");
				$this->post("filter")->ALL("/^add\/([^\/]*)\/filter$/");

				$this->get("publish", "publish")->ALL("/^add\/([^\/]*)\/publish$/");
				$this->post("publish")->ALL("/^add\/([^\/]*)\/publish$/");
			}
		}
		if(substr(\lib\router::get_url(), 0, 8) === 'add/tree')
		{
			$this->display_name = 'content_u\\add\\tree.html';
			$this->get('tree','tree')->ALL(
			[
				'url' => "/^add\/tree/",
				'property' =>
				[
					"search"     => ["/^.*$/", true, 'search'],
					"page"       => ["/^\d+$/", true, 'page'],
					"repository" => ["/^.*$/", true, 'repository']
				]
			]);

		}

		// add new
		$this->get(false, "add")->ALL("/^add$/");
		$this->post("add")->ALL("/^add$/");

		// for add survey
		// $this->get("survey", "survey")->ALL("/^add\/(.*)$/");
		$this->post("add")->ALL("/^add\/([^\/]*)$/");
		$this->get("edit", "edit")->ALL("/^add\/([^\/]*)$/");
		// $this->post("auto_save")->ALL("/^add\/(.*)$/");
	}
}
?>