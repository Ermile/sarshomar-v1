<?php
namespace content_u\add;

class controller extends \content_u\main\controller
{
	/**
	 * route add poll
	 */
	public function _route()
	{
		parent::_route();

		// need less to load eny thing
		$this->get("add")->SERVER("/^add$/");

		// load data to ready to update
		$this->get("edit", "edit")->SERVER("/^add\/([". self::$shortURL. "]+)$/");

		// add new poll
		$this->post("add")->SERVER("/^add$/");
		// edit a poll
		$this->post("edit")->SERVER("/^add\/([". self::$shortURL. "]+)$/");
	}
}
?>