<?php
namespace content_u\polls;

class view extends \mvc\view
{

	/**
	 * sho last question to answer user
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function view_show($o)
	{
		$this->data->datatable = $o->api_callback;
	}

	function config()
	{
		$this->include->fontawesome = true;
	}
}
?>