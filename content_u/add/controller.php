<?php
namespace content_u\add;

class controller extends \content_u\home\controller
{
	function _route()
	{

		// check login
		parent::check_login();

		if(substr(\lib\router::get_url(), 0, 8) === 'add/tree')
		{
			$this->display_name = 'content_u\\add\\tree.html';
			$this->get('tree','tree')->SERVER(
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
		$this->get(false, false)->SERVER("/^add$/");
		$this->post("add")->SERVER("/^add$/");

		// for add survey
		// $this->get("survey", "survey")->SERVER("/^add\/(.*)$/");

		$this->post("edit")->SERVER("/^add\/([". self::$shortURL. "]+)$/");
		$this->get("edit", "edit")->SERVER("/^add\/([". self::$shortURL. "]+)$/");
		
		$this->get("filter", "filter")->SERVER("/^add\/([". self::$shortURL. "]+)\/filter$/");
		$this->post("filter")->SERVER("/^add\/([". self::$shortURL. "]+)\/filter$/");


		$this->get("publish", "publish")->SERVER("/^add\/([". self::$shortURL. "]+)\/publish$/");
		$this->post("publish")->SERVER("/^add\/([". self::$shortURL. "]+)\/publish$/");
	}
}
?>