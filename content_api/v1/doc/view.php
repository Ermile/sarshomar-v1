<?php
namespace content_api\v1\doc;

class view extends  \mvc\view
{
	public function config()
	{
		$this->data->doc                  = [];
		$this->data->doc['base']          = 'content_api/v1/doc/template/';
		$this->data->doc['start']         = $this->data->doc['base'].'start/';
		$this->data->doc['poll']          = $this->data->doc['base'].'poll/';
		$this->data->doc['login']         = $this->data->doc['base'].'login/';
		$this->data->doc['billing']       = $this->data->doc['base'].'billing/';
		$this->data->doc['profile']       = $this->data->doc['base'].'profile/';

		$this->data->template['poll_add'] = 'content_api/v1/doc/template/poll_add.html';
		$this->data->template['urls']     = 'content_api/v1/doc/template/urls.html';
		$this->data->template['sample']   = 'content_api/v1/doc/template/sample.html';
	}
}
?>