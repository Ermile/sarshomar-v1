<?php
namespace content_admin\logs;

class controller extends \content_admin\home\controller
{
	public function _route()
	{
		parent::check_login();

		$property           = [];
		$property['search'] = ["/^.*$/", true, 'search'];
		$this->get("logs", "logs")->ALL(['property' => $property]);
	}
}

?>