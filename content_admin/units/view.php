<?php
namespace content_admin\units;

class view extends \mvc\view
{
	/**
	 * { function_description }
	 */
	function config()
	{
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