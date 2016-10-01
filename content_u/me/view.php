<?php
namespace content_u\me;

class view extends \mvc\view
{
	public function view_me($_args)
	{
		$this->data->me = $_args->api_callback;
	}
}
?>