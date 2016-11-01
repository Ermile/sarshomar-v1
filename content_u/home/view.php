<?php
namespace content_u\home;

class view extends \mvc\view
{

	/**
	 * view profile of user
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function config()
	{
		$this->include->fontawesome = true;
	}

	function view_profile($o)
	{
		$user_id                       = $this->login("id");
		$dashboard_data                = \lib\db\profiles::get_dashboard_data($user_id);
		$this->data->dashboard_data    = $dashboard_data;
	}
}
?>