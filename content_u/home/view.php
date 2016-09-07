<?php
namespace content_u\home;

class view extends \mvc\view
{

	/**
	 * view profile of user
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function view_profile($o)
	{
		$this->data->profile = $o->api_callback;
	}
}
?>