<?php
namespace content_u\profile;

class view extends \mvc\view
{
	public function view_profile($_args)
	{
		$this->data->set_pin = $this->model()->have_pin();
		$this->data->profile = $_args->api_callback;

	}
}
?>