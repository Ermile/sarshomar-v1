<?php
namespace content_api\help;

class view extends  \mvc\view
{	
	public function config()
	{
		$this->data->template['poll_add'] = 'content_api/help/template/poll_add.html';

	}
}
?>