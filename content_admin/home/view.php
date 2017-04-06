<?php
namespace content_admin\home;

class view extends \mvc\view
{
	function config()
	{

		$this->data->visitors = \lib\utility\visitor::chart();

		$this->show_chart();
	}


	public function show_chart()
	{
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