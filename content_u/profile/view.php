<?php
namespace content_u\profile;

class view extends \mvc\view
{
	public function view_profile($_args)
	{
		$this->data->set_pin = $this->model()->have_pin();
		$me = $_args->api_callback;
		// var_dump($me);exit();
		$this->data->profile = $me;
	}
}
?>