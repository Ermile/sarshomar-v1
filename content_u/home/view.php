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

		if(\lib\utility::get('remove_account') == 'i_am_tester')
		{
			$this->model()->remove_account();
		}
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
		// \lib\utility\profiles::refresh_dashboard($user_id);
		$this->data->ui_language = \lib\utility\users::get_language($user_id);
		$dashboard_data                = \lib\db\userdashboards::get($user_id);

		if(!is_array($dashboard_data))
		{
			$dashboard_data = [];
		}

		// $complete_profile = \lib\utility\profiles::get_profile_data($user_id, false);
		// if(!is_array($complete_profile))
		// {
		// 	$complete_profile = 0;
		// }
		// else
		// {
		// 	$complete_profile = array_filter($complete_profile);
		// 	$complete_profile = count($complete_profile) * 2;
		// 	if($complete_profile > 100)
		// 	{
		// 		$complete_profile = 100;
		// 	}

		// }
		// $this->data->complete_profile = $complete_profile;

		// $draft_count    = \lib\db\polls::get_count(null,
		// [
		// 	'user_id'        => $user_id,
		// 	'in'             => 'me',
		// 	'check_language' => false,
		// 	'login'          => $user_id,
		// 	'post_status'    => 'draft',
		// ]);

		// $publish_count  = \lib\db\polls::get_count(null,
		// [
		// 	'user_id'        => $user_id,
		// 	'in'             => 'me',
		// 	'check_language' => false,
		// 	'login'          => $user_id,
		// 	'post_status'    => 'publish',
		// ]);

		// $awaiting_count = \lib\db\polls::get_count(null,
		// [
		// 	'user_id'        => $user_id,
		// 	'in'             => 'me',
		// 	'check_language' => false,
		// 	'login'          => $user_id,
		// 	'post_status'    => 'awaiting',
		// ]);

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
			'my_like'            => 0,
			'my_fav'             => 0,
		];

		$dashboard_data = array_merge($temp_dashboar_data, $dashboard_data);
		// $dashboard_data['draft_count']    =	$draft_count;
		// $dashboard_data['publish_count']  = $publish_count;
		// $dashboard_data['awaiting_count'] = $awaiting_count;
		$dashboard_data['sarshomar_poll'] = $sarshomar_poll;

		$meta =
		[
			'get_count' => true,
			'log_data'  => $this->login('id'),
		];

		$dashboard_data['user_referred'] = \lib\db\logs::search(null, array_merge($meta, ['caller' => 'user:ref:set']));
		$dashboard_data['user_verified'] = \lib\db\logs::search(null, array_merge($meta, ['caller' => 'user:ref:signup']));


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