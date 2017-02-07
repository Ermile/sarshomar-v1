<?php
namespace content\referer\instagram;

class view extends \content\home\view
{
	function config()
	{
		$this->controller->display_name = 'content/referer/instagram/display.html';
		$this->data->page['title'] = 'Access Token APIs';
		$this->data->token = substr(\lib\utility::get('to'), 6);
	}

	function view_instagram($_args)
	{
		if(isset($_args->api_callback['user']))
		{
			$this->data->oauth = $_args->api_callback['user'];
		}
	}
}
?>