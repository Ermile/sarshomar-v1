<?php
namespace content_admin\transactions;

class controller extends \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$property           = [];
		$property['search'] = ["/^.*$/", true, 'search'];
		$property['type']   = ["/^.*$/", true, 'type'];
		$property['mobile'] = ["/^.*$/", true, 'mobile'];
		$property['caller'] = ["/^.*$/", true, 'caller'];
		$property['user']   = ["/^.*$/", true, 'user'];
		$property['date']   = ["/^.*$/", true, 'date'];
		$property['order']  = ["/^.*$/", true, 'order'];
		$property['sort']   = ["/^.*$/", true, 'sort'];
		$property['time']   = ["/^.*$/", true, 'time'];

		$this->get("transactions", "transactions")->ALL(['property' => $property]);
	}
}

?>