<?php
namespace content_u\add\filter;

trait view 
{
	/**
	 * ready to load fieter page
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function view_filter($_args)
	{
		// add all template of question into new file
		$this->data->template['filter']['layout'] = 'content_u/add/filter/layout.html';

		// get poll_id || suervey_id from url
		$poll_survey_id = $this->model()->check_poll_url($_args);
		$url = 'add/'. \lib\utility\shortURL::encode($poll_survey_id);

		$this->data->step =
		[
			'current'      => 'filter',
			'add'          => true,
			'filter'       => true,
			'publish'      => false,
			'link_add'     => $url,
			'link_filter'  => $url. '/filter',
			'link_publish' => $url. '/publish'
		];

		
		$this->data->filters   = $filters;
		$this->data->member    = \lib\db\ranks::get($poll_survey_id, 'member');

		// check is_survey or no
		// if(!\lib\utility\survey::is_survey($poll_survey_id))
		// {
		// 	// if user remove polls and redirect to this page
		// 	// we change the survey to poll and redirect to poll/filter
		// 	$url = \lib\utility\survey::change_to_poll($poll_survey_id);
		// 	if(is_string($url))
		// 	{
		// 		$this->redirector()->set_url("@/$url/filter")->redirect();
		// 	}
		// }
	}
}
?>