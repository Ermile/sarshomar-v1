<?php
namespace content_admin\attachments;

class controller extends \content_admin\main\controller
{
	public function _route()
	{
		parent::_route();

		$property           = [];
		$property['search'] = ["/^.*$/", true, 'search'];
		$property['status'] = ["/^.*$/", true, 'status'];
		$property['sort']   = ["/^.*$/", true, 'sort'];
		$property['order']  = ["/^.*$/", true, 'order'];
		$this->get("attachments", "attachments")->ALL(['property' => $property]);
		$this->get("show", "show")->ALL("/attachments\/show\/id\=\d+/");
		$this->post("show")->ALL("/attachments\/show\/id\=\d+/");
		$this->post("accept")->ALL("/^attachments$/");
	}
}

?>