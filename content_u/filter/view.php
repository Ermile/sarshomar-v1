<?php
namespace content_u\filter;

class view extends \mvc\view
{

	/**
	 * ready to load fieter page
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function view_filter($_args)
	{
		$this->include->fontawesome = true;

		// get poll_id || suervey_id from url
		$poll_survey_id = $this->model()->check_poll_url($_args);

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

		// save poll_id to form
		$this->data->poll_id = $poll_survey_id;
		// get existing filter list to load in html and user can select this
		$this->data->filter_list = $_args->api_callback;
				// array (size=6)
		  // 'public' =>
		  //   array (size=3)
		  //     'gender' =>
		  //       array (size=2)
		  //         0 => string 'male' (length=4)
		  //         1 => string 'female' (length=6)
		  //     'marrital' =>
		  //       array (size=2)
		  //         0 => string 'single' (length=6)
		  //         1 => string 'marriade' (length=8)
		  //     'language' => null
		  // 'education' =>
		  //   array (size=3)
		  //     'graduation' =>
		  //       array (size=3)
		  //         0 => string 'illiterate' (length=10)
		  //         1 => string 'undergraduate' (length=13)
		  //         2 => string 'graduate' (length=8)
		  //     'degree' =>
		  //       array (size=7)
		  //         0 => string 'under diploma' (length=13)
		  //         1 => string 'diploma' (length=7)
		  //         2 => string '2 year college' (length=14)
		  //         3 => string 'bachelor' (length=8)
		  //         4 => string 'master' (length=6)
		  //         5 => string 'phd' (length=3)
		  //         6 => string 'other' (length=5)
		  //     'course' => null
		  // 'family' =>
		  //   array (size=2)
		  //     'age' => null
		  //     'range' =>
		  //       array (size=7)
		  //         0 => string '-13' (length=3)
		  //         1 => string '14-17' (length=5)
		  //         2 => string '18-24' (length=5)
		  //         3 => string '25-30' (length=5)
		  //         4 => string '31-44' (length=5)
		  //         5 => string '45-59' (length=5)
		  //         6 => string '60+' (length=3)
		  // 'job' =>
		  //   array (size=2)
		  //     'employmentstatus' =>
		  //       array (size=3)
		  //         0 => string 'employee' (length=8)
		  //         1 => string 'unemployee' (length=10)
		  //         2 => string 'retired' (length=7)
		  //     'industry' => null
		  // 'location' =>
		  //   array (size=4)
		  //     'country' => null
		  //     'province' => null
		  //     'city' => null
		  //     'housestatus' =>
		  //       array (size=3)
		  //         0 => string 'owner' (length=5)
		  //         1 => string 'tenant' (length=6)
		  //         2 => string 'homeless' (length=8)
		  // 'other' =>
		  //   array (size=2)
		  //     'internetusage' =>
		  //       array (size=3)
		  //         0 => string 'low' (length=3)
		  //         1 => string 'mid' (length=3)
		  //         2 => string 'high' (length=4)
		  //     'religion' => null
	}
}
?>