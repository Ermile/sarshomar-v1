<?php
namespace content_admin\users;

class controller extends \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$property           = [];
		$property['search'] = ["/^.*$/", true, 'search'];
		$property['mobile'] = ["/^.*$/", true, 'mobile'];
		$property['caller'] = ["/^.*$/", true, 'caller'];
		$property['user']   = ["/^.*$/", true, 'user'];
		$property['date']   = ["/^.*$/", true, 'date'];
		$property['order']  = ["/^.*$/", true, 'order'];
		$property['sort']   = ["/^.*$/", true, 'sort'];
		$property['time']   = ["/^.*$/", true, 'time'];

		$this->get("users", "users")->ALL(['property' => $property]);
	}
}

?>