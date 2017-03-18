<?php
namespace content_admin\logitems;

class controller extends \content_admin\home\controller
{
	public function _route()
	{
		parent::check_login();

		$property           = [];
		$property['search'] = ["/^.*$/", true, 'search'];
		$this->get("logitems", "logitems")->ALL(['property' => $property]);
	}
}

?>