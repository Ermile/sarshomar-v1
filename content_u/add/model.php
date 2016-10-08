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
		// $result = \lib\db\survey::set($poll_id);

		// exit();
	}

	/**
	 * get data to add new add
	 */
	function post_add($_args)
	{
		// default survey id is null
		$survey_id = null;
		// db poll type
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

		// users click on one of this buttom
		if(utility::post("filter"))
		{
			$poll_survey = "poll";
			if($this->check_poll_url($_args))
			{
				$survey_id   = $this->check_poll_url($_args);
			}
			// if users click on this buttom and nothing in page
			// redirect to filter page
			if($title == null || (!is_array($answers) || count($answers) < 2))
			{
				if($survey_id)
				{
					$survey_id   = $this->check_poll_url($_args);
					// encode survey id
					$short_url = \lib\utility\shortURL::encode($survey_id);
					// must be redirect to filter page
					$this->redirector()->set_url("@/$short_url/filter");
					// return;
				}
				else
				{
					debug::error(T_("undefined error"));
					return false;;
				}
			}

		}
		elseif(utility::post("survey"))
		{
			$poll_survey = "survey";
		}
		elseif(utility::post("add_poll"))
		{
			$poll_survey = "poll";
			$survey_id   = $this->check_poll_url($_args);
		}
		else
		{
			debug::error(T_("command not found"));
			return false;
		}

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
		// ready to insert poll or survey
		// save survey
		if($poll_survey == "survey")
		{
			$args =
			[
				'user_id'      => $this->login('id'),
				'title'        => 'untitled survey',
				'type'         => 'survey_private',
				'language'     => $language,
				'status'       => 'draft',
			];
			$survey_id = \lib\db\survey::insert($args);
			$poll_type = "survey_poll_". $poll_type;
		}
		else
		{
			// if users adding new poll to sorvey
			// $poll_survey = 'poll' and $suervey_id is full
			if($survey_id)
			{
				$poll_type = "survey_poll_". $poll_type;
			}
			else
			{
				$poll_type = "poll_private_". $poll_type;
			}
		}
		$args =
		[
			'user_id'      => $this->login('id'),
			'title'        => $title,
			'type'         => $poll_type,
			'content'      => $content,
			'parent'	   => $survey_id,
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
			$answers = \lib\db\answers::insert($answers_arg);
		}
		else
		{
			debug::error(T_("answers not found"));
			return false;
		}
		// get the metas of this poll
		$metas = [];
		$insert_meta = false;
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
					$insert_meta = true;
				}
			}
		}
		if($insert_meta)
		{
			$save_poll_metas = \lib\db\options::insert_multi($metas);
		}
		if($answers)
		{
			\lib\debug::true(T_("Add $poll_survey Success"));
			if(($poll_survey == "poll" && !$survey_id) || utility::post("filter"))
			{
				if($this->check_poll_url($_args))
				{
					$survey_id   = $this->check_poll_url($_args);
					// encode survey id
					$short_url = \lib\utility\shortURL::encode($survey_id);
				}
				else
				{
					// encode poll id
					$short_url = \lib\utility\shortURL::encode($poll_id);
				}
				// must be redirect to filter page
				$this->redirector()->set_url("@/$short_url/filter");
				return;
			}
			else
			{
				$short_url = \lib\utility\shortURL::encode($survey_id);
				debug::msg($short_url);
				$this->redirector()->set_url("@/$short_url/add");
			}
		}
		else
		{
			\lib\debug::error(T_("Error in add $poll_survey"));
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
		$result  = \lib\db\polls::get_for_edit($poll_id);
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
	public function check_poll_url($_args, $_type = "decode")
	{
		if(isset($_args->match->url[0]) && is_array($_args->match->url[0]))
		{
			$url = $_args->match->url[0][1];
			if($_type == "decode")
			{
				return \lib\utility\shortURL::decode($url);
			}
			else
			{
				return $url;
			}
		}
		else
		{
			// \lib\debug::error(T_("poll id not found"));
			return false;
		}
	}


	/**
	*	get add filter
	*/
	function get_filter($_args)
	{

		// $filters =
		// [
		// 	"gender" =>
		// 	[
		// 		"male"
		// 	]
		// ];

		// $num = \lib\db\filters::count_filtered_member($filters);
		// list of adds filter
		$filter_list = \lib\db\filters::get_exist_filter();
		return $filter_list;
	}


	public function post_filter($_args)
	{
		// get filter
		$filter = array_filter(utility::post());

		// get count member by tihs filter
		$count_filtered_member = \lib\db\filters::count_filtered_member($filter);

		if($count_filtered_member < 1)
		{
			debug::error(T_("max = $count_filtered_member and this is less than 100, remove some filter"));
			return false;
		}

		$poll_id = $this->check_poll_url($_args);

		$args = [];
		foreach ($filter as $key => $value) {
			$args[$key] = $value;
		}

		$result = \lib\db\filters::insert($poll_id, $args);
		if(!$result)
		{
			$result = \lib\db\options::update_on_error($args);
		}
		if($result)
		{
			$short_url = $this->check_poll_url($_args, "encode");
			\lib\debug::true(T_("add filter of poll Success"));
			$this->redirector()->set_url("@/$short_url/publish");

		}
		else
		{
			\lib\debug::error(T_("Error in insert filter of poll"));
		}
	}


	/**
	 * ready to publish
	 * if one poll set and type is survey change type and return
	 *
	 * @param      <type>  $_args  The arguments
	 */
	function get_publish($_args)
	{
		$short_url = \lib\db\polls::get_poll_url($this->check_poll_url($_args));
		return $short_url;
		// check users to load cat and article
	}


	function post_publish($_args)
	{
		$poll_survey_id = $this->check_poll_url($_args);
		if(!$poll_survey_id)
		{
			debug::error(T_("id not found"));
			return false;
		}

		// insert tags to tags table,
		// @param string
		// @example : tag1,tag2,tag3,...
		// split by ',' and insert
		$tags = utility::post("tags");
		if($tags)
		{
			$insert_tag = \lib\db\tags::insert_multi($tags);

			$tags_id    = \lib\db\tags::get_multi_id($tags);

			// save tag to this poll
			$useage_arg = [
				'termusage_foreign' => 'posts',
				'tags'              => $tags_id,
				'termusage_id'      => $poll_survey_id
			];

			$useage = \lib\db\termuseage::insert_multi($useage_arg);
		}

		$date_start = utility::post("date_start");
		$date_end   = utility::post("date_end");

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
			$article =
			[
				'post_id' => $poll_survey_id,
				'option_cat' => "poll_$poll_survey_id",
				'option_key' => "article",
				'option_value' => utility::post("article")
			];
			$article = \lib\db\options::insert($article);
		}

		$update_posts =
		[
			'post_status' => 'publish',
			'language'     => $language,
			'publish_date' => $publish_date,
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