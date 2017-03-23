<?php
namespace content_admin\logitems;

class controller extends \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$property           = [];
		$property['search'] = ["/^.*$/", true, 'search'];
		$this->get("logitems", "logitems")->ALL(['property' => $property]);
	}
}

?>