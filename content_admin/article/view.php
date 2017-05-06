<?php
namespace content_admin\article;

class view extends \content_admin\main\view
{
	public function view_article($_args)
	{
		$this->data->article = $_args->api_callback;
	}
}
?>