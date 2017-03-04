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
		$this->data->template['progress']        = 'content_u/add/progress.html';

		if($this->module() === 'home')
		{
			// $this->data->bodyclass        = 'dashboard';

		}
		$this->data->page['title'] = T_("Dashboard");
		$this->data->displayname = T_($this->login("displayname"));
	}


	/**
	 * view profile data
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function view_profile($o)
	{

		if(!$this->login())
		{
			return false;
		}

		$user_id                       = $this->login("id");

		$this->data->ui_language = \lib\db\users::get_language($user_id);

		$dashboard_data                = \lib\utility\profiles::get_dashboard_data($user_id);
		if(!is_array($dashboard_data))
		{
			$dashboard_data = [];
		}

		$complete_profile = \lib\utility\profiles::get_profile_data($user_id, false);
		if(!is_array($complete_profile))
		{
			$complete_profile = 0;
		}
		else
		{
			$complete_profile = array_filter($complete_profile);
			$complete_profile = count($complete_profile) * 2;
			if($complete_profile > 100)
			{
				$complete_profile = 100;
			}

		}
		$this->data->complete_profile = $complete_profile;

		$draft_count    = \lib\db\polls::get_count(null,
		[
			'user_id'        => $user_id,
			'in'             => 'me',
			'check_language' => false,
			'login'          => $user_id,
			'post_status'    => 'draft',
		]);

		$publish_count  = \lib\db\polls::get_count(null,
		[
			'user_id'        => $user_id,
			'in'             => 'me',
			'check_language' => false,
			'login'          => $user_id,
			'post_status'    => 'publish',
		]);

		$awaiting_count = \lib\db\polls::get_count(null,
		[
			'user_id'        => $user_id,
			'in'             => 'me',
			'check_language' => false,
			'login'          => $user_id,
			'post_status'    => 'awaiting',
		]);

		$sarshomar_poll = \lib\db\polls::get_count(null);

		$temp_dashboar_data =
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
		$dashboard_data = array_merge($dashboard_data, $temp_dashboar_data);
		foreach ($dashboard_data as $key => $value)
		{
			$dashboard_data[$key] = (int) $value;
		}
		$this->data->dashboard = $dashboard_data;
		$remain = (int) $dashboard_data['sarshomar_poll'];
		$remain -= (int) $dashboard_data['poll_answered'];
		$remain -= (int) $dashboard_data['poll_skipped'];

		$chart_data   = [];
		$chart_data[] = ["key" => T_("Remain"),   "value" => $dashboard_data['sarshomar_poll']];
		$chart_data[] = ["key" => T_("Answered"), "value" => $dashboard_data['poll_answered']];
		$chart_data[] = ["key" => T_("Skipped"),  "value" => $dashboard_data['poll_skipped']];

		$this->data->chart_data = json_encode($chart_data, JSON_UNESCAPED_UNICODE);
	}
}
?>