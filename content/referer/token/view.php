<?php
namespace content\referer\token;

class view extends \content\home\view
{
	function config()
	{
		$this->controller->display_name = 'content/referer/token/display.html';
		$this->data->page['title'] = 'Access Token APIs';
		$this->data->token = substr(\lib\utility::get('to'), 6);
	}
}
?>