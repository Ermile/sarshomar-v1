<?php
namespace content_admin\home;

class view extends \mvc\view
{
	function config()
	{

		$this->data->visitors = \lib\utility\visitor::chart();
		if(\lib\utility::get('refresh_all_chart') == 'refresh_all_chart')
		{
			\lib\utility\stat_polls::refresh_all();
			if(\lib\debug::$status)
			{
				\lib\debug::true(T_("All chart data refreshed"));
			}
			else
			{
				\lib\debug::error(T_("Error in refresh chart data"));
			}
		}

		if(\lib\utility::get('refresh_chart') && preg_match("/^[". SHORTURL_ALPHABET ."]+$/", \lib\utility::get('refresh_chart')))
		{
			\lib\utility\stat_polls::refresh(\lib\utility\shortURL::decode(\lib\utility::get('refresh_chart')));
			if(\lib\debug::$status)
			{
				\lib\debug::true(T_("Poll chart data refreshed"));
			}
			else
			{
				\lib\debug::error(T_("Error in refresh chart data of this poll"));
			}
		}
		$this->show_chart();
	}


	public function show_chart()
	{
		$this->data->number_static = $this->model()->numbers();
		$this->data->chart_polls   = json_encode($this->model()->chart_polls(),JSON_UNESCAPED_UNICODE);
		$this->data->chart_users   = json_encode($this->model()->chart_users(),JSON_UNESCAPED_UNICODE);
		$this->data->chart_visitor = json_encode($this->model()->chart_visitor(),JSON_UNESCAPED_UNICODE);
		$this->data->chart_answer  = json_encode($this->model()->chart_answer(),JSON_UNESCAPED_UNICODE);
	}
}
?>