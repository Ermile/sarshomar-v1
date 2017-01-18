<?php
namespace content_api\help;

class controller extends  \mvc\controller
{
	public function __construct()
	{
		\lib\storage::set_api(false);
		parent::__construct();
	}


	public function _route()
	{
		$this->get(false,false)->ALL("/.*/");
	}
}
?>