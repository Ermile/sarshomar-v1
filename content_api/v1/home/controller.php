<?php
namespace content_api\v1\home;

class controller extends  \mvc\controller
{
	public function __construct()
	{
		$url = \lib\router::get_url();
		if($url == 'v1')
		{
			\lib\storage::set_api(false);
		}
		else
		{
			\lib\storage::set_api(true);
		}

		parent::__construct();
	}

	public function _route()
	{
		$url = \lib\router::get_url();

		if($url == 'v1')
		{
			$this->redirector('v1/doc')->redirect();
			return;
		}
	}

	public function corridor()
	{
		if(!$this->method && $_SERVER['REQUEST_METHOD'] !== 'GET')
		{
			\lib\error::method($_SERVER['REQUEST_METHOD'] . " not allowed");
		}
	}
}
?>