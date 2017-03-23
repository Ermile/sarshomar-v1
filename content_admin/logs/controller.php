<?php
namespace content_admin\logs;

class controller extends \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$property           = [];
		$property['search'] = ["/^.*$/", true, 'search'];
		$this->get("logs", "logs")->ALL(['property' => $property]);
	}
}

?>