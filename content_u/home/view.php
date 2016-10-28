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

		// get total poll this users answered to that
		$this->data->total_answered    = $dashboard_data['pollanswer'];
		// get total poll this users skipped that
		$this->data->total_skipped     = $dashboard_data['pollskipped'];
		// get total answere + skipped polls of this users
		$this->data->total             = $this->data->total_answered + $this->data->total_skipped;
		// the point of all answered
		$this->data->point             = $dashboard_data['point'];
		$dashboard_data['surveycount'];
		// get count of polls of this users (this user creat it)
		$this->data->user_polls        = $dashboard_data['pollcount'];
		$this->data->people_answered   = $dashboard_data['peopleanswer'];
		$this->data->people_skipped    = $dashboard_data['peopleskipped'];
		// count referral users
		$this->data->awaiting_referral = $dashboard_data['userreferred'];
		$this->data->active_referral   = $dashboard_data['userverified'];

		$this->data->profile           = $o->api_callback;
	}
}
?>