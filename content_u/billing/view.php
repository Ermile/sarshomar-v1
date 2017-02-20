<?php
namespace content_u\billing;

class view extends \mvc\view
{
	public function config()
	{
		$this->data->amount = \lib\utility::get('amount');
	}


	public function view_billing($_args)
	{

	}
}
?>