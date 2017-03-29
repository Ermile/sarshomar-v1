<?php
namespace content_admin\logitems;

class controller extends \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$property           = [];
		$property['search'] = ["/^.*$/", true, 'search'];
		$property['sort']   = ["/^.*$/", true, 'sort'];
		$property['order']  = ["/^.*$/", true, 'order'];
		$this->get("logitems", "logitems")->ALL(['property' => $property]);
	}
}

?>