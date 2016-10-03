<?php
namespace content_u\add;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{
	/**
	 * get users question
	 *
	 * @return     <type>  The question.
	 */
	function get_knowledge()
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
	 * ready to publish
	 * if one poll set and type is survey change type and return
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function get_publish($_args)
	{

	}


	function post_publish()
	{
		var_dump(utility::post());
		exit();

	}


	/**
	 * set survey.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_set_survey($_args)
	{
		$poll_id = $_args->match->url[0][1];
		if($poll_id)
		{
			$poll_id = \lib\utility\shortURL::decode($poll_id);
		}
		$result = \lib\db\survey::set($poll_id);

		exit();
	}

	/**
	 * get data to add new add
	 */
	function post_add()
	{
		if(utility::post("filter"))
		{
			$poll_survey = "poll";
		}
		elseif(utility::post("survey"))
		{
			$poll_survey = "survey";
		}
		else
		{
			debug::error(T_("command not found"));
			return false;
		}

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
			debug::error(T_("poll type not found"));
			return false;
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
		// get answers true
		$answer_true  = utility::post("answer_true");
		// get answers point
		$answer_point  = utility::post("answer_point");
		// get answers desc
		$answer_desc  = utility::post("answer_desc");
		// get answers
		$answers      = utility::post("answers");
		// get summary of poll
		$summary      = utility::post("summary");

		// check title
		if($title == null)
		{
			debug::error(T_("poll title can not null"));
			return;
		}

		// check length of sumamry text
		if($summary && strlen($summary) > 150)
		{
			debug::error(T_("summary text must be less than 150 character"));
			return false;
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
			'status'       => 'draft',
			'meta'         => "{\"desc\":\"$summary\"}"
		];

		// inset poll and answers
		$poll_id = \lib\db\polls::insert($args);

		// check answers
		if($answers)
		{
			// if answers is not array return false
			if(!is_array($answers))
			{
				debug::error(T_("answer must be array"));
				return false;
			}
			// remove empty index from answer array
			$answers = array_filter($answers);
			// check the count of answer array
			if(count($answers) < 2)
			{
				debug::error(T_("you must set two answer"));
				return false;
			}
		}

		// combine answer type and answer text
		$combine = [];
		foreach ($answers as $key => $value) {
			$combine[] = [
							'true'  => isset($answer_true[$key])  ? $answer_true[$key] 	: null,
							'point' => isset($answer_point[$key]) ? $answer_point[$key] : null,
							'type'  => isset($answer_type[$key])  ? $answer_type[$key] 	: null,
							'txt'   => $value
						 ];
		}

		$answers_arg =
		[
			'poll_id' => $poll_id,
			'answers' => $combine
		];

		$answers    = \lib\db\answers::insert($answers_arg);

		// insert tags to tags table,
		// @param string
		// @example : tag1,tag2,tag3,...
		// split by ',' and insert
		// $insert_tag = \lib\db\tags::insert_multi($tags);

		// $tags_id    = \lib\db\tags::get_multi_id($tags);

		// save tag to this poll
		// $useage_arg = [
		// 	'termusage_foreign' => 'posts',
		// 	'tags'              => $tags_id,
		// 	'termusage_id'      => $poll_id
		// ];

		// $useage = \lib\db\termuseage::insert_multi($useage_arg);

		// get the metas of this poll
		$metas = [];
		foreach (utility::post() as $key => $value) {
			if(preg_match("/^meta\_(.*)$/", $key, $meta))
			{
				if(isset($meta[1]))
				{
					$metas[] =
					[
						'post_id'      => $poll_id,
						'option_cat'   => "poll_$poll_id",
						'option_key'   => 'meta',
						'option_value' => $meta[1]
					];
				}
			}
		}
		$save_poll_metas = \lib\db\options::insert_multi($metas);

		if($answers)
		{
			$short_url = \lib\utility\shortURL::encode($poll_id);
			\lib\debug::true(T_("Add poll Success @/$short_url/filter"));
			if($poll_survey == "poll")
			{
				// must be redirect to filter page
				$this->redirector()->set_url("@/$short_url/filter");
				return;
			}
			else
			{
				debug::msg($short_url);
			}
		}
		else
		{
			\lib\debug::error(T_("Error in add poll"));
		}
	}


	/**
	 * get one poll id and return data of this poll
	 * ready for edit form
	 *
	 * @param      <type>  $_args      { parameter_description }
	 *
	 * @return     <type>  The poll edit.
	 */
	function get_edit($_args)
	{
		$poll_id = $_args->match->url[0][1];
		$poll_id = \lib\utility\shortURL::decode($poll_id);
		$result = \lib\db\polls::get_for_edit($poll_id);
		return $result;
	}


	/**
	 * post edited value of add and update add
	 *
	 * @param      <type>  $_args      { parameter_description }
	 */
	function post_edit($_args){

		$poll_id = $_args->match->url[0][1];
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
			\lib\debug::true(T_("Edit poll Success"));
		}
		else
		{
			\lib\debug::error(T_("Error in Edit poll"));
		}
	}


	/**
	 * delete poll
	 */
	function get_delete()
	{

	}


	/**
	 * check short url and return the poll id
	 */
	public function check_poll_url($_args)
	{

		if(isset($_args->match->url[0][1]))
		{
			$url = $_args->match->url[0][1];
			return \lib\utility\shortURL::decode($url);
		}
		else
		{
			\lib\debug::error(T_("poll id not found"));
			return false;
		}
	}


	/**
	*	get add filter
	*/
	function get_filter($_args)
	{
		$this->check_poll_url($_args);
		// list of adds filter
		// get value from cash or user profile status
		$add_filters =
		[
			'age_min',
			'age_max',
			'members_min',
			'members_max',
			'public_answer',
			'date_start',
			'date_end',
			'time_start',
			'time_end',
			'count_true'
		];

		// get user detail filter
		// example gender, age, city , ...
		$user_detail_filter = \lib\db\filters::get();
		if(!is_array($user_detail_filter))
		{
			$user_detail_filter = [];
		}

		$filters = [];
		foreach ($user_detail_filter as $key => $value)
		{
			if(!isset($filters[$value['key']]))
			{
				$filters[$value['key']] = [$value['value']];
			}
			else
			{
				array_push($filters[$value['key']], $value['value']);
			}
		}

		$result['user_detail_filter'] = $filters;
		$result['add_filters'] = $add_filters;

		return $result;
	}


	public function post_filter($_args)
	{

		$args = [];

		$poll_id = $this->check_poll_url($_args);
		$args['poll_id'] = $poll_id;

		$filters = utility::post();
		foreach ($filters as $key => $value) {
			$args[$key] = $value;
		}

		$result = \lib\db\filters::insert($args);
		if($result)
		{
			$short_url = $_args->match->url[0][1];
			\lib\debug::true(T_("add filter of poll Success @/$short_url/publish"));
		}
		else
		{
			\lib\debug::error(T_("Error in insert filter of poll"));
		}
	}
}
?>