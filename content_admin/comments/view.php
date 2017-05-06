<?php
namespace content_admin\comments;

class view extends \content_admin\main\view
{
	function view_comments($_args)
	{
		$this->data->comments = $_args->api_callback;
	}
}
?>