<?php
namespace content_api\v1\doc;

class view extends  \mvc\view
{
	public function config()
	{
		$this->data->template['poll_add'] = 'content_api/v1/doc/template/poll_add.html';
		$this->data->template['urls']     = 'content_api/v1/doc/template/urls.html';
		$this->data->template['sample']   = 'content_api/v1/doc/template/sample.html';
	}
}
?>