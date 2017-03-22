<?php
namespace content_u\billing;

class view extends \mvc\view
{
	public function config()
	{
		$this->data->amount = \lib\utility::get('amount');
		$this->data->page['title'] = T_("Billing");

	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_billing($_args)
	{
		$history = $_args->api_callback;
		$this->data->history = $history;
	}

}
?>