<?php
namespace content_u\security;

class view extends \mvc\view
{

	public function config()
	{
		$this->data->page['title'] = T_("Security");
	}

	public function view_security($_args)
	{

	}
}
?>