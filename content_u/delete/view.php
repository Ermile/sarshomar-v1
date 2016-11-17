<?php
namespace content_u\delete;

class view extends \mvc\view
{
	public function view_delete($_args)
	{
		$this->data->delete = $_args->api_callback;
	}
}
?>