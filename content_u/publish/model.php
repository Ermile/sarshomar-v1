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
		$poll_survey_id       = $this->check_poll_url($_args);
		$this->poll_survey_id = $poll_survey_id;
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
			if(!is_array($tags_id))
			{
				$tags_id = [];
			}
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

		// dave start date and end date in post_meta
		$this->date_start_end("start_time");
		$this->date_start_end("end_time");

		if($this->access('u', 'sarshomar_knowledge', 'add'))
		{
			if(utility::post("article"))
			{
				$article =
				[
					'post_id'      => $poll_survey_id,
					'option_cat'   => "poll_$poll_survey_id",
					'option_key'   => "article",
					'option_value' => utility::post("article")
				];
				$article_insert = \lib\db\options::insert($article);
				if(!$article_insert)
				{
					\lib\db\options::update_on_error($article, array_splice($article, 1));
				}
			}

			if(utility::post("cat"))
			{
				\lib\db\cats::set(utility::post("cat"), $poll_survey_id);
			}
		}

		$language = utility::post("language");

		$publish_status = 'publish';

		$post_status = \lib\db\polls::get_poll_status($poll_survey_id);
		if($post_status == 'awaiting')
		{
			$publish_status = $post_status;
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


	/**
	 * save publish date and date of stop poll
	 * in option table and meta of poll
	 *
	 * @param      <type>  $_type  The type
	 */
	function date_start_end($_type)
	{

		$publish_date =
		[
			'post_id'      => $this->poll_survey_id,
			'option_cat'   => "poll_{$this->poll_survey_id}",
			'option_key'   => $_type,
			'option_value' => utility::post($_type)
		];
		$publish_date_query = \lib\db\options::insert($publish_date);
		if(!$publish_date_query)
		{
			\lib\db\options::update_on_error($publish_date, array_splice($publish_date, 1));
			\lib\db\polls::replace_meta([$_type => utility::post($_type)], $this->poll_survey_id);
		}
		else
		{
			\lib\db\polls::merge_meta([$_type => utility::post($_type)], $this->poll_survey_id);
		}
	}
}
?>