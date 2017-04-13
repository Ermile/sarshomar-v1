<?php
namespace content_admin\ranks;

class controller extends \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$property           = [];
		$property['search'] = ["/^.*$/", true, 'search'];
		$property['sort']   = ["/^.*$/", true, 'sort'];
		$property['order']  = ["/^.*$/", true, 'order'];
		$this->get("ranks", "ranks")->ALL(['property' => $property]);
	}
}

?>