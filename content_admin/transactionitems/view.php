<?php
namespace content_admin\transactionitems;

class view extends \content_admin\main\view
{
	/**
	 * { function_description }
	 */
	function config()
	{
		$this->data->transactionitems = \lib\db\transactionitems::get();
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