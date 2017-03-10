<?php
namespace lib\utility\profiles;

trait dashboard
{

	public static $dashboard_data =
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
		'draft_count'        => 0,
		'publish_count'      => 0,
		'awaiting_count'     => 0,
	];

	use querys;

	/**
	 * refresh dashboar data
	 *
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function refresh_dashboard($_user_id)
	{
		// return true;
		$querys    = self::dashboard_query($_user_id);
		$run_query = [];

		foreach ($querys as $title => $query)
		{
			$cat = "user_detail_". $_user_id;
			$key = "dashboard_data";
			// $run_query =
			$run_query[] =
			"
				INSERT INTO options
				SET
					options.user_id       = $_user_id,
					options.option_cat    = '$cat',
					options.option_key    = '$key',
					options.option_value  = '$title',
					options.option_meta   = ($query),
					options.option_status = 'enable'
				ON DUPLICATE KEY UPDATE
					options.user_id       = $_user_id,
					options.option_meta   = ($query),
					options.option_status = 'enable'
			";
			// \lib\db::query($run_query);
		}
		$run_query = implode(';', $run_query);
		\lib\db::query($run_query . ";", true, ['multi_query' => true]);

		return true;
	}


	/**
	 * Gets the dashboard data.
	 * some field in users table
	 * @param      <type>  $_user_id  The user identifier
	 *
	 * @return     <type>  The dashboard data.
	 */
	public static function get_dashboard_data($_user_id)
	{
		$query =
		"
			SELECT
				option_value AS `title`,
				option_meta AS `count`
			FROM
				options
			WHERE
				post_id IS NULL AND
				user_id       = $_user_id AND
				option_cat    = 'user_detail_$_user_id' AND
				option_key    = 'dashboard_data' AND
				option_status = 'enable'
			-- profiles::get_dashboard_data()
		";

		$dashboard = \lib\db::get($query, ['title', 'count']);
		$dashboard['user_referred'] = self::user_ref($_user_id);
		$dashboard['user_verified'] = self::user_ref($_user_id, 'active');
		return $dashboard;
	}


	/**
	 * Sets the dashboard data.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_title    The title
	 */
	public static function set_dashboard_data($_user_id, $_title, $_plus = 1)
	{
		$args =
		[
			'post_id'      => null,
			'user_id'      => $_user_id,
			'option_cat'   => 'user_detail_'. $_user_id,
			'option_key'   => 'dashboard_data',
			'option_value' => $_title,
		];
		\lib\db\options::plus($args, $_plus);
	}


	/**
	 * Sets the dashboard data.
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_title    The title
	 */
	public static function minus_dashboard_data($_user_id, $_title, $_minus = 1)
	{
		$args =
		[
			'post_id'      => null,
			'user_id'      => $_user_id,
			'option_cat'   => 'user_detail_'. $_user_id,
			'option_key'   => 'dashboard_data',
			'option_value' => $_title,
		];
		\lib\db\options::minus($args, $_minus);
	}


	/**
	 * save count of people see my poll
	 * load this data in dashboard
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_poll_id  The poll identifier
	 */
	public static function people_see_my_poll($_poll_id, $_title, $_type)
	{
		$poll = \lib\db\polls::get_poll($_poll_id);
		$poll_type = null;
		if(isset($poll['type']))
		{
			$poll_type = $poll['type'];
		}

		$user_id = null;

		if(isset($poll['user_id']))
		{
			$user_id = $poll['user_id'];
		}

		$value = "my_". $poll_type. "_". $_title;

		$where =
		[
			'user_id'      => $user_id,
			'post_id'      => null,
			'option_cat'   => 'user_detail_'. $user_id,
			'option_key'   => 'dashboard_data',
			'option_value' => $value
		];

		if($_type === 'plus')
		{
			\lib\db\options::plus($where);
		}
		else
		{
			\lib\db\options::minus($where);
		}
	}


	/**
	 * get count of user refrred
	 *
	 * @param      <type>  $_user_id  The user identifier
	 * @param      <type>  $_type     The type
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function user_ref($_user_id, $_type = null)
	{
		if($_type === null)
		{
			$type = null;
		}
		else
		{
			$type = " AND users.user_status = '$_type' ";
		}

		$query =
		"
			SELECT
				COUNT(users.id) AS 'count'
			FROM
				users
			WHERE
				users.user_parent = $_user_id
				$type
		";
		return \lib\db::get($query, 'count', true);
	}
}
?>