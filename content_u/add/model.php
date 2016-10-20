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
		$_args =['user_id'   => $user_id];
		return \lib\db\polls::xget($_args);
	}


	/**
	 * get data to add new add
	 */
	function post_add($_args)
	{
		// start transaction
		\lib\db::transaction();

		// default survey id is null
		$survey_id = null;
		// users click on one of 'add filter' buttom
		if(utility::post("filter"))
		{
			$insert_poll = null;
			// if title is null and answer is null
			// we check the url
			// if in the survey we abrot save poll and redirect to filter page
			// user discard the poll
			if(utility::post("title") == '' && empty(array_filter(utility::post("answers"))))
			{
				// if we not in survey we have error for title and answers
				if(!$this->check_poll_url($_args))
				{
					debug::error(T_("title or answers must be full"));
				}
			}
			else
			{
				// insert the poll
				$insert_poll = $this->insert_poll();
			}

			// check the url
			if($this->check_poll_url($_args))
			{
				// url like this >> @/(.*)/add
				$url       = $this->check_poll_url($_args, "encode");
				$survey_id = $this->check_poll_url($_args);
				// save survey title
				$this->set_suervey_title($survey_id);
			}
			else
			{
				// the url is @/add
				$url = \lib\utility\shortURL::encode($insert_poll);
			}
			if(debug::$status)
			{
				// must be redirect to filter page
				$this->redirector()->set_url("@/$url/filter");
			}
		}
		elseif(utility::post("survey"))
		{
			// the user click on this buttom
			// we save the survey
			$args =
			[
				'user_id'      => $this->login('id'),
				'title'        => 'untitled survey',
				'type'         => 'survey_private',
				'status'       => 'draft',
			];
			$survey_id = \lib\db\survey::insert($args);
			// change type of the poll of this suervey to 'survey_poll_[polltype - media - image , text,  ... ]'
			$poll_type = "survey_poll_";
			// insert the poll
			$insert_poll = $this->insert_poll(['poll_type' => $poll_type, 'survey_id' => $survey_id]);
			// redirect to @/$url/add to add another poll
			$url = \lib\utility\shortURL::encode($survey_id);
			if($insert_poll)
			{
				$this->redirector()->set_url("@/$url/add");

			}

		}
		elseif(utility::post("add_poll"))
		{
			//users click on this buttom
			// change type of the poll of this suervey to 'survey_poll_[polltype - media - image , text,  ... ]'
			$poll_type = "survey_poll_";
			// get the survey id and survey url
			$survey_id = $this->check_poll_url($_args, "decode");
			$survey_url = $this->check_poll_url($_args, "encode");
			// insert the poll
			$insert_poll = $this->insert_poll(['poll_type' => $poll_type, 'survey_id' => $survey_id]);
			// save survey title
			$this->set_suervey_title($survey_id);
			// redirect to '@/survey id /add' to add another poll
			if($insert_poll)
			{
				$this->redirector()->set_url("@/$survey_url/add");
			}
		}
		// users click on this buttom
		elseif(utility::post("publish"))
		{
			$insert_poll = null;
			// if title is null and answer is null
			// we check the url
			// if in the survey we abrot save poll and redirect to filter page
			// user discard the poll
			if(utility::post("title") == '' && empty(array_filter(utility::post("answers"))))
			{
				// if we not in survey we have error for title and answers
				if(!$this->check_poll_url($_args))
				{
					debug::error(T_("title or answers must be full"));
				}
			}
			else
			{
				// insert the poll
				$insert_poll = $this->insert_poll();
			}

			// check the url
			if($this->check_poll_url($_args))
			{
				// url like this >> @/(.*)/add
				$url       = $this->check_poll_url($_args, "encode");
				$survey_id = $this->check_poll_url($_args);
				// save survey title
				$this->set_suervey_title($survey_id);
			}
			else
			{
				// the url is @/add
				$url = \lib\utility\shortURL::encode($insert_poll);
			}
			if(debug::$status)
			{
				// must be redirect to publish page
				$this->redirector()->set_url("@/$url/publish");
			}
		}
		else
		{
			// the user click on buttom was not support us !!
			debug::error(T_("command not found"));
		}

		if(debug::$status)
		{
			\lib\db::commit();
		}
		else
		{
			\lib\db::rollback();
		}
	}

	/**
	 * Sets the suervey title.
	 */
	public function set_suervey_title($_survey_id)
	{
		//save survey name
		if(utility::post("survey_title"))
		{
			// polls::update_url() has retrun  '$/[shortURL of survey id ]/suervy_title'
			$args =
			[
				'post_title' => utility::post("survey_title"),
				'post_url'   => \lib\db\polls::update_url($_survey_id, utility::post("survey_title"), false),
				'post_slug'  => \lib\utility\filter::slug(utility::post("survey_title"))
			];
			$result = \lib\db\survey::update($args, $_survey_id);
			if(!$result)
			{
				debug::error(T_("error in save survey title"));
			}
		}
	}

	/**
	 * insert poll
	 * get data from utility::post()
	 *
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function insert_poll($_options = [])
	{
		// get poll_type
		$poll_type    = utility::post("poll_type");
		// swich html name and db name of poll type
		switch ($poll_type) {
			case 'multiple_choice':
				$poll_type = 'select';
				break;

			case 'descriptive':
				$poll_type = 'text';
				break;

			case 'notification':
				$poll_type = 'notify';
				break;

			case 'upload':
				$poll_type = 'upload';
				break;

			case 'starred':
				$poll_type = 'star';
				break;

			case 'numerical':
				$poll_type = 'number';
				break;

			case 'sort':
				$poll_type = 'order';
				break;

			// $poll_type = 'media_image';
			// $poll_type = 'media_video';
			// $poll_type = 'media_audio';

			default:
				debug::error(T_("poll type not found"));
				return false;
				break;
		}
		// get poll type from function args
		if(isset($_options['poll_type']))
		{
			$poll_type = $_options['poll_type']. $poll_type;
		}
		else
		{
			$poll_type = 'poll_private_'. $poll_type;
		}
		// check the suevey id to set in post_parent
		if(isset($_options['survey_id']))
		{
			$survey_id = $_options['survey_id'];
		}
		else
		{
			$survey_id = null;
		}
		// get title
		$title        = utility::post("title");
		// get content
		$content      = utility::post("content");
		// get summary of poll
		$summary      = utility::post("summary");
		// get answers
		$answers      = utility::post("answers");
		// get answers type
		$answer_type  = utility::post("answer_type");
		// get answers true
		$answer_true  = utility::post("answer_true");
		// get answers point
		$answer_point = utility::post("answer_point");
		// get answers desc
		$answer_desc  = utility::post("answer_desc");

		// check title
		if($title == null)
		{
			debug::error(T_("poll title can not null"));
			return false;
		}
		// check length of sumamry text
		if($summary && strlen($summary) > 150)
		{
			debug::error(T_("summary text must be less than 150 character"));
			return false;
		}
		// ready to inset poll
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
		// inset poll
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
			// combine answer type and answer text and answer point
			$combine = [];
			foreach ($answers as $key => $value) {
				$combine[] =
				[
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
			\lib\debug::true(T_("add poll Success"));
			return $poll_id;
		}
		else
		{
			\lib\debug::error(T_("Error in add poll"));
			return false;
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
		// list of adds filter
		$filter_list = \lib\db\filters::get_exist_filter();
		return $filter_list;
	}


	/**
	 * save filter
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function post_filter($_args)
	{
		// get filter
		// remove empty filters in post
		$post = array_filter(utility::post());
		$filter = [];
		// get the post started by 'filter_' string
		foreach ($post as $key => $value) {
			if(preg_match("/^filter\_(.*)$/", $key, $name))
			{
				$filter[$name[1]] = $value;
			}
		}
		// very filter seleced
		if(count($filter) > 5)
		{
			debug::error(T_("oops, too many filters. remove some filter"));
			return false;
		}
		// get count member by tihs filter
		$count_filtered_member = \lib\db\filters::count_filtered_member($filter);

		debug::warn(T_(":max members founded",["max" => $count_filtered_member]));

		if($count_filtered_member < 1)
		{
			debug::error(T_("max = :max and this is less than 100, remove some filter",["max" => $count_filtered_member]));
			return false;
		}
		// get the poll or survey id
		$poll_id = $this->check_poll_url($_args);

		if(!$poll_id)
		{
			debug::error(T_("poll id not found"));
			return false;
		}
		// ready to insert filters in options table
		$args = [];
		foreach ($filter as $key => $value) {
			$args[] =
			[
				'post_id'      => $poll_id,
				'option_cat'   => "poll_$poll_id",
				'option_key'   => $key,
				'option_value' => $value,
				'option_meta'  => null
			];
		}
		$result = \lib\db\options::insert_multi($args);
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
		// get poll url to show in publish form
		$short_url = \lib\db\polls::get_poll_url($this->check_poll_url($_args));
		return $short_url;
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
		$language = utility::post("language");

		$update_posts =
		[
			'post_status'   => 'publish',
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