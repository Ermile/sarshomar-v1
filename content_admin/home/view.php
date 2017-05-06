<?php
namespace content_admin\home;

class view extends \content_admin\main\view
{
	function config()
	{

		// $this->data->visitors = \lib\utility\visitor::chart();

		$this->show_chart();
	}


	public function show_chart()
	{
		if(\lib\utility::get('refresh_dashboard'))
		{
			$this->model()->numbers(true);
			$this->model()->chart_update('polls');
			$this->model()->chart_update('users_week');
			$this->model()->chart_update('users');
			$this->model()->chart_update('visitor');
			$this->model()->chart_update('answer');
			$this->model()->chart_update('transactions');
		}

		$this->data->number_static = $this->model()->numbers();
		$this->data->chart_polls   = json_encode($this->model()->chart_polls(),JSON_UNESCAPED_UNICODE);
		$this->data->chart_visitor = json_encode($this->model()->chart_visitor(),JSON_UNESCAPED_UNICODE);
		$this->data->chart_answer  = json_encode($this->model()->chart_answer(),JSON_UNESCAPED_UNICODE);

		if(\lib\utility::get('signup') == 'week')
		{
			$this->data->chart_users   = json_encode($this->model()->chart_users_week(),JSON_UNESCAPED_UNICODE);
		}
		else
		{
			$this->data->chart_users   = json_encode($this->model()->chart_users(),JSON_UNESCAPED_UNICODE);
		}
	}

}
?>