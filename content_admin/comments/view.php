<?php
namespace content_admin\comments;

class view extends \mvc\view
{
	function view_comments($_args)
	{
		$this->data->comments = $_args->api_callback;
		// get fontawesome class
		$this->include->fontawesome = true;
	}
}
?>