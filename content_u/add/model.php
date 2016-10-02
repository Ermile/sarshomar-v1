<?php
namespace content_u\add;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * get users add
	 *
	 * @return     <type>  The add.
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
	 * get data to add new add
	 */
	function post_add()
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
		if($answer_type)
		{
			$answer_type = array_filter($answer_type);
		}

		// get answers true
		$answer_true  = utility::post("answer_true");
		if($answer_true)
		{
			$answer_true = array_filter($answer_true);
		}

		// get answers point
		$answer_point  = utility::post("answer_point");
		if($answer_point)
		{
			$answer_point = array_filter($answer_point);
		}

		// get answers desc
		$answer_desc  = utility::post("answer_desc");
		if($answer_desc)
		{
			$answer_desc = array_filter($answer_desc);
		}

		// get answers
		$answers      = utility::post("answers");

		// get tags
		$tags         = utility::post("tags");

		// check title
		if($title == null)
		{
			debug::error(T_("add title can not null"));
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

		if(!is_array($answers))
		{
			$answers = [];
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

		// get the addons of this poll
		$addons = [];
		foreach (utility::post() as $key => $value) {
			if(preg_match("/^addon\_(.*)$/", $key, $addon))
			{
				$addons[] = $addon[1];
			}
		}
		// var_dump($addons);exit();

		if($answers)
		{

			$short_url = \lib\utility\shortURL::encode($poll_id);
			\lib\debug::msg($short_url);
			\lib\debug::true(T_("Add add Success"));
		}
		else
		{
			\lib\debug::error(T_("Error in add add"));
		}
	}


	/**
	 * get one add id and return data of this add
	 * ready for edit form
	 *
	 * @param      <type>  $o      { parameter_description }
	 *
	 * @return     <type>  The add edit.
	 */
	function get_edit($o)
	{
		$poll_id = $o->match->url[0][1];
		return \lib\db\polls::get_one($poll_id);
	}


	/**
	 * post edited value of add and update add
	 *
	 * @param      <type>  $o      { parameter_description }
	 */
	function post_edit($o){

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
			\lib\debug::true(T_("Edit add Success"));
		}
		else
		{
			\lib\debug::error(T_("Error in Edit add"));
		}
	}


	/**
	 * delete add
	 */
	function get_delete()
	{

	}


	/**
	 * check short url and return the poll id
	 */
	public function check_poll_url()
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
		$this->check_poll_url();
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

		$poll_id = $this->check_poll_url();
		$args['poll_id'] = $poll_id;

		$filters = utility::post();
		foreach ($filters as $key => $value) {
			$args[$key] = $value;
		}

		$result = \lib\db\filters::insert($args);
		if($result)
		{
			\lib\debug::true(T_("add filter of add Success"));
		}
		else
		{
			\lib\debug::error(T_("Error in insert filter of add"));
		}
	}
}
?>