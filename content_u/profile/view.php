<?php
namespace content_u\profile;

class view extends \mvc\view
{
	public function view_profile($_args)
	{
		$this->data->profile = $_args->api_callback;
	}
}
?>