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
		if(!$this->login())
		{
			return;
		}

		$history = $_args->api_callback;
		$this->data->history = $history;
		$user_cash_all = \lib\db\transactions::budget($this->login('id'), ['unit' => 'all']);
		$this->data->user_cash_all = $user_cash_all;
	}

}
?>