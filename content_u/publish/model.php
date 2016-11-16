<?php
namespace content_u\publish;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{


	/**
	 * ready to publish
	 * if one poll set and type is survey change type and return
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function get_publish($_args)
	{
		$poll_id = $this->check_poll_url($_args);
		// get poll url to show in publish form
		$poll = \lib\db\polls::get_poll($poll_id);
		return $poll;
		// check users to load cat and article

	}


	/**
	 * save publish data
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	function post_publish($_args)
	{
		$poll_survey_id = $this->check_poll_url($_args);
		if(!$poll_survey_id)
		{
			debug::error(T_("poll id not found"));
			return false;
		}

		// insert tags to tags table,
		// @param string
		// @example : tag1,tag2,tag3,...
		// split by ',' and insert
		$tags = utility::post("tags");

		$remove_tags = \lib\db\tags::remove($poll_survey_id);

		if($tags)
		{
			// check count tags
			$check_count = explode(',', $tags);
			$check_count = array_filter($check_count);
			if(count($check_count) > 3 && !$this->access('u', 'sarshomar_knowledge', 'add'))
			{
				debug::error(T_("too tags added !!! remove some tags"));
				return;
			}

			$insert_tag = \lib\db\tags::insert_multi($tags);

			$tags_id    = \lib\db\tags::get_multi_id($tags);

			// save tag to this poll
			$useage_arg = [];
			foreach ($tags_id as $key => $value) {

				$useage_arg[] =
				[
					'termusage_foreign' => 'posts',
					'term_id'           => $value,
					'termusage_id'      => $poll_survey_id
				];
			}
			$useage = \lib\db\termusages::insert_multi($useage_arg);
		}

		$date_start = utility::post("start_time");
		$date_end   = utility::post("end_time");
		// dave start date and end date in post_meta
		$update_post_meta = \lib\db\polls::merge_meta(['date_start' => $date_start, 'date_end' => $date_end], $poll_survey_id);

		// set publish date
		$publish_date = [];
		if($date_start)
		{
			$publish_date[] =
			[
				'post_id'      => $poll_survey_id,
				'option_cat'   => "poll_$poll_survey_id",
				'option_key'   => "date_start",
				'option_value' => $date_start
			];
		}

		if($date_end)
		{
			$publish_date[] =
			[
				'post_id' => $poll_survey_id,
				'option_cat' => "poll_$poll_survey_id",
				'option_key' => "date_end",
				'option_value' => $date_end
			];
		}

		if(count($publish_date) == 2)
		{
			$publish_date = \lib\db\options::insert_multi($publish_date);
		}
		elseif(count($publish_date) == 1)
		{
			$publish_date = \lib\db\options::insert($publish_date[0]);
		}
		if(utility::post("article"))
		{
			if($this->access('u', 'sarshomar_knowledge', 'add'))
			{
				$article =
				[
					'post_id' => $poll_survey_id,
					'option_cat' => "poll_$poll_survey_id",
					'option_key' => "article",
					'option_value' => utility::post("article")
				];
				$article = \lib\db\options::insert($article);
			}
		}

		$language = utility::post("language");

		$post_status = \lib\db\polls::get_poll_status($poll_survey_id);
		if($post_status != 'publish')
		{
			$publish_status = $post_status;
		}
		else
		{
			$publish_status = 'publish';
		}

		// save and check words
		if(!\lib\db\words::save_and_check(utility::post()))
		{
			$publish_status = 'awaiting';
			\lib\debug::warn(T_("You have to use words that are not approved in the text, Your text comes into review mode"));
			\lib\debug::msg('spam', \lib\db\words::$spam);
		}

		$update_posts =
		[
			'post_status'   => $publish_status,
			'post_language' => $language
		];

		$result = \lib\db\polls::update($update_posts, $poll_survey_id);

		if($result)
		{
			debug::true(T_("poll published"));
			$url = \lib\db\polls::get_poll_url($poll_survey_id);
			$this->redirector()->set_url("$url");

		}
		else
		{
			debug::error(T_("error in publish poll"));
		}
	}
}
?>