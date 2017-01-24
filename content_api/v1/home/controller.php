<?php
namespace content_api\v1\home;

class controller extends  \mvc\controller
{
	public function __construct()
	{
		\lib\storage::set_api(false);
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
}
?>