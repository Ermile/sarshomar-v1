<?php
namespace content_u\add\filter;

class view extends \content_u\home\view
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

		$this->data->step =
		[
			'current'      => 'filter',
			'add'          => true,
			'filter'       => true,
			'publish'      => false,
			'link_add'     => 'add...',
			'link_filter'  => 'filter....',
			'link_publish' => 'publish.....'
		];
		$this->include->fontawesome = true;

		// get poll_id || suervey_id from url
		$poll_survey_id = $this->model()->check_poll_url($_args);

		$this->page_progress_url($poll_survey_id, "filter");

		$filters = \lib\db\filters::get_poll_filter($poll_survey_id);

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