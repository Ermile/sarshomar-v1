<?php
namespace content_u\question;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * get users question
	 *
	 * @return     <type>  The question.
	 */
	function get_question()
	{

		// in one page can be display 10 record of polls
		$page   = 1;
		$lenght = 10;

		$user_id = 1;
		$user_id = $this->login('id');

		// set args to load query
		$_args =[
				'user_id'   => $user_id,
				// 'post_type' =>  $user_id,
				// 'post_status' => "draft",
				'page'      => $page,
				'lenght'    => $lenght
				];

		return \lib\db\polls::xget($_args);
	}


	/**
	 * get data to add new question
	 */
	function post_question_add()
	{
		$db_poll_type =
		[
			'select',
			'notify',
			'text',
			'upload',
			'star',
			'number',
			'media_image',
			'media_video',
			'media_audio',
			'order'
		];
		// get poll_type
		$poll_type    = utility::post("poll_type");
		// check poll type. users can be set $db_poll_type
		if(!in_array($poll_type, $db_poll_type))
		{
			$poll_type = "select";
		}

		// get title
		$title        = utility::post("title");
		// get language
		$language     = utility::post("language");
		// get content
		$content      = utility::post("content");
		// get publish date
		$publish_date = utility::post("publish_date");
		// get answers type
		$answer_type  = utility::post("answer_type");
		// get answers
		$answers      = utility::post("answers");
		// get tags
		$tags         = utility::post("tags");
		// get count people to quese this poll
		$count        = utility::post("count");
		// gnerate cats from posts
		// @example cat_programing, cat_student
		// @return [programin => on, student => on]
		$cats = [];
		foreach (utility::post() as $key => $value)
		{
			if(preg_match("/cat\_(.*)/", $key, $cat))
			{
				$cats[$cat[1]] = $value;
			}
		}

		//check login
		if(!$this->login('id'))
		{
			$this->redirector()->set_domain()->set_url('login')->redirect();
		}

		// check title
		if($title == null)
		{
			debug::error(T_("Question title can not null"));
			return;
		}

		//check lang
		if($language == null)
		{
			$language = substr(\lib\router::get_storage('language'), 0, 2);
		}

		//check date
		if($publish_date == null)
		{
			if($language == 'fa')
			{
				// get persion date
				$publish_date = utility\jdate::date("Y-m-d");
			}
			else
			{
				$publish_date = date("Y-m-d");
			}
		}

		// ready to insert poll
		$args =
		[
			'user_id'      => $this->login('id'),
			'title'        => $title,
			'type'         => 'private_' . $poll_type,
			'language'     => $language,
			'content'      => $content,
			'publish_date' => $publish_date,
			'status'       => 'draft'
		];

		// inset poll and answers
		$poll_id = \lib\db\polls::insert($args);

		$answers_arg =
		[
			'poll_id' => $poll_id,
			'answers' => $answers
		];

		$answers    = \lib\db\answers::insert($answers_arg);

		// insert tags to tags table,
		// @param string
		// @example : tag1,tag2,tag3,...
		// split by ',' and insert
		$insert_tag = \lib\db\tags::insert_multi($tags);

		$tags_id    = \lib\db\tags::get_multi_id($tags);

		// save tag to this poll
		$useage_arg = [
			'termusage_foreign' => 'posts',
			'tags'              => $tags_id,
			'termusage_id'      => $poll_id
		];
		$useage = \lib\db\termuseage::insert_multi($useage_arg);

		if($answers)
		{
			\lib\debug::true(T_("Add Question Success"));
		}
		else
		{
			\lib\debug::error(T_("Error in add question"));
		}
	}


	/**
	 * get one question id and return data of this question
	 * ready for edit form
	 *
	 * @param      <type>  $o      { parameter_description }
	 *
	 * @return     <type>  The question edit.
	 */
	function get_question_edit($o)
	{
		$poll_id = $o->match->url[0][1];
		return \lib\db\polls::get_one($poll_id);
	}


	/**
	 * post edited value of question and update question
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function post_question_edit($o){

		$poll_id = $o->match->url[0][1];
			$args = [
				'post_title'       => utility::post("title"),
				'post_language'    => utility::post("language"),
				'post_content'     => utility::post("content"),
				'post_publishdate' => utility::post("publish_date"),
				];

		$result = \lib\db\polls::update($args, $poll_id);

		foreach (utility::post('answers') as $key => $value)
		{
			\lib\db\answers::update(['option_value' => $value], $key);
		}

		if($result)
		{
			\lib\debug::true(T_("Edit Question Success"));
		}
		else
		{
			\lib\debug::error(T_("Error in Edit question"));
		}
	}


	/**
	 * delete question
	 */
	function get_question_delete()
	{

	}
}
?>