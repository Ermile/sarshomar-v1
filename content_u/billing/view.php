<?php
namespace content_u\billing;

class view extends \mvc\view
{
	public function config()
	{
		$this->data->amount = \lib\utility::get('amount');
		$this->data->page['title'] = T_("Billing");

	}


	public function view_billing($_args)
	{

	}
}
?>