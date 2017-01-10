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

		// load html of filter and publish
		if(preg_match("/add\/([". self::$shortURL. "]+)\/(filter|publish)$/", \lib\router::get_url(), $load))
		{
			if(isset($load[2]))
			{
				$this->display_name = "\\content_u\\add\\$load[2]\\display.html";
			}
		}

		// need less to load eny thing
		$this->get(false, false)->SERVER("/^add$/");

		// add new poll
		// $this->post("add")->SERVER("/^add$/");

		// edit a poll
		// alise of put in api mode
		// $this->post("edit")->SERVER("/^add\/([". self::$shortURL. "]+)$/");

		// load data to ready to update
		// $this->get("edit", "edit")->SERVER("/^add\/([". self::$shortURL. "]+)$/");

		// load filter data to update it
		// $this->get("get", "filter")->SERVER("/^add\/([". self::$shortURL. "]+)\/filter$/");

		// edit poll whit filters
		// $this->post("filter")->SERVER("/^add\/([". self::$shortURL. "]+)\/filter$/");

		// load publish data to edit it
		// $this->get("publish", "publish")->SERVER("/^add\/([". self::$shortURL. "]+)\/publish$/");

		// edit publish data
		// $this->post("publish")->SERVER("/^add\/([". self::$shortURL. "]+)\/publish$/");
		
		// for add survey
		// $this->get("survey", "survey")->SERVER("/^add\/(.*)$/");
	}
}
?>