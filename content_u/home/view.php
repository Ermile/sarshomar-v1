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
		if($this->module() === 'home')
		{
			$this->data->bodyclass        = 'dashboard';
			$this->include->chart         = true;

		}
	}

	function view_profile($o)
	{

		$user_id                       = $this->login("id");
		$dashboard_data                = \lib\utility\profiles::get_dashboard_data($user_id);
		if(!is_array($dashboard_data))
		{
			$dashboard_data = [];
		}

		$complete_profile = \lib\utility\profiles::get_profile_data($user_id);
		$complete_profile = array_filter($complete_profile);
		$complete_profile = count($complete_profile) * 2;
		if($complete_profile > 100)
		{
			$complete_profile = 100;
		}
		$this->data->complete_profile = $complete_profile;

		$draft_count = \lib\db\polls::get_count(null, ['user_id' => $this->login('id'), 'post_status' => 'draft']);
		$publish_count = \lib\db\polls::get_count(null, ['user_id' => $this->login('id'), 'post_status' => 'publish']);
		$awaiting_count = \lib\db\polls::get_count(null, ['user_id' => $this->login('id'), 'post_status' => 'awaiting']);
		$sarshomar_poll = \lib\db\polls::get_count(null, ['post_sarshomar' => 1]);
		$this->data->dashboard =
		[
			'poll_answered'      => 0,
			'poll_skipped'       => 0,
			'survey_answered'    => 0,
			'survey_skipped'     => 0,
			'my_poll'            => 0,
			'my_survey'          => 0,
			'my_poll_answered'   => 0,
			'my_poll_skipped'    => 0,
			'my_survey_answered' => 0,
			'my_survey_skipped'  => 0,
			'user_referred'      => 0,
			'user_verified'      => 0,
			'comment_count'      => 0,
			'draft_count'        => ($draft_count) ? $draft_count : 0,
			'publish_count'      => ($publish_count) ? $publish_count : 0,
			'awaiting_count'     => ($awaiting_count) ? $awaiting_count : 0,
			'sarshomar_poll'     => ($sarshomar_poll) ? $sarshomar_poll : 0
		];

		foreach ($dashboard_data as $key => $value) {
			$this->data->dashboard[$key] = $value;
		}
	}
}
?>