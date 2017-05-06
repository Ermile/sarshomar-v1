<?php
namespace content_admin\exchangerates;

class view extends \content_admin\main\view
{
	/**
	 * { function_description }
	 */
	function config()
	{
		$this->data->exchangerates = \lib\db\exchangerates::get();
		$this->data->units = \lib\db\units::get();
	}


	/**
	 * get all transactions record
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function view_add($_args)
	{
	}


	/**
	 * ready to edit transaction items
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function view_edit($_args)
	{
		$this->data->item = $_args->api_callback;
	}
}
?>