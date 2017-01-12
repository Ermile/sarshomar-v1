<?php
namespace content_api\answer;

class controller extends  \mvc\controller
{	
	public function __construct()
	{
		\lib\storage::set_api(true);
		parent::__construct();
	}

	public function _route()
	{
		
	}
}
?>