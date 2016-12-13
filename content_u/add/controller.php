<?php
namespace content_u\add;

class controller extends \content_u\home\controller
{
	function _route()
	{

		// check login
		parent::check_login();

		$shortURL = "[23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]+";

		if(preg_match("/add\/(". $shortURL. ")\/(filter|publish)$/", \lib\router::get_url(), $load))
		{
			if(isset($load[2]))
			{
				$this->model_name   = "\\content_u\\add\\$load[2]\\model";
				$this->view_name    = "\\content_u\\add\\$load[2]\\view";
				$this->display_name = "\\content_u\\add\\$load[2]\\display.html";

				$this->get("filter", "filter")->ALL("/^add\/(". $shortURL. ")\/filter$/");
				$this->post("filter")->ALL("/^add\/(". $shortURL. ")\/filter$/");

				$this->get("publish", "publish")->ALL("/^add\/(". $shortURL. ")\/publish$/");
				$this->post("publish")->ALL("/^add\/(". $shortURL. ")\/publish$/");
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

		// add new poll
		$this->get(false, false)->ALL("/^add$/");
		$this->post("add")->ALL("/^add$/");

		// for add survey
		// $this->get("survey", "survey")->ALL("/^add\/(.*)$/");

		$this->post("add")->ALL("/^add\/(". $shortURL. ")$/");
		$this->get("edit", "edit")->ALL("/^add\/(". $shortURL. ")$/");
	}
}
?>