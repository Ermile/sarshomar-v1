<?php
namespace content_u\add\model;
use \lib\utility;
use \lib\debug;

trait survey
{

	/**
	 * Sets the suervey title.
	 */
	public function set_suervey_title($_survey_id)
	{
		//save survey name
		if(utility::post("secondary-title"))
		{
			// polls::update_url() has retrun  '$/[shortURL of survey id ]/suervy_title'
			$slug = \lib\utility\filter::slug(utility::post("secondary-title"));
			if(strlen($slug) > 100)
			{
				$slug = substr($slug, 0, 99);
			}

			// $url = \lib\db\polls::update_url($_survey_id, utility::post("secondary-title"), false);
			$url = null;
			if(strlen($url) > 255)
			{
				$url = substr($url, 0, 254);
			}

			$title =  utility::post("secondary-title");
			if(strlen($title) > 200)
			{
				$title = substr($title, 0, 199);
			}

			$survey_status = 'draft';
			// save and check words
			if(!\lib\db\words::save_and_check($title))
			{
				$survey_status = 'awaiting';
				\lib\debug::warn(T_("You are using an inappropriate word in the text, your poll is awaiting moderation", 'secondary-title'));
				\lib\debug::msg('spam', \lib\db\words::$spam);
			}

			$args =
			[
				'post_title'  => $title,
				'post_url'    => $url,
				'post_gender' => 'survey',
				'post_status' => $survey_status,
				'post_slug'   => $slug
			];
			$result = \lib\utility\survey::update($args, $_survey_id);
			if(!$result)
			{
				debug::error(T_("error in save survey title"), 'title');
			}

		}
	}


	/**
	 * set survey.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_survey($_args)
	{
		$poll_id = $_args->match->url[0][1];
		if($poll_id)
		{
			$poll_id = \lib\utility\shortURL::decode($poll_id);
		}
	}
}
?>