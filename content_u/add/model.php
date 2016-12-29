<?php
namespace content_u\add;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{
	use model\insert_poll;
	use model\survey;
	use model\tree;

	function post_filter(){}
	function post_publish(){}

	/**
	 * update mod
	 * @var        boolean
	 */
	private $update_mode = false;


	/**
	* the poll id
	* @var        integer
	*/
	private $poll_id     = 0;


	/**
	 * Gets the edit.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The edit.
	 */
	public function get_edit($_args)
	{
		$poll_id = $this->check_poll_url($_args);
		return $poll_id;
	}


	/**
	 * get data to add new add
	 */
	function post_add($_args)
	{
		// check user id
		if(!$this->login("id"))
		{
			debug::error(T_("Please login to insert a poll"));
			return;
		}

		// check sarshomar knowlege permission
		$this->sarshomar = false;
		if($this->access('u', 'sarshomar_knowledge', 'add'))
		{
			$this->sarshomar = true;
		}

		/**
		 * update the poll or survey
		 */
		$this->update = false;
		if($this->check_poll_url($_args))
		{
			$this->update = $this->check_poll_url($_args, "encode");
		}

		// default survey id is null
		$survey_id = null;
		// users click on one of 'filter' buttom
		if(utility::post("filter"))
		{
			// insert the poll
			$insert_poll = $this->insert_poll();

			// check the url
			if($this->check_poll_url($_args))
			{
				// url like this >> @/(.*)/add
				$url = $this->check_poll_url($_args, "encode");
			}
			else
			{
				// the url is @/add
				$url = \lib\utility\shortURL::encode($insert_poll);
			}

			if(debug::$status)
			{
				// must be redirect to filter page
				$this->redirector()->set_url($this->url('prefix'). "/add/$url/filter");
			}
		}
		elseif(utility::post("survey"))
		{
			// // the user click on this buttom
			// // we save the survey
			// $args =
			// [
			// 	'user_id'        => $this->login('id'),
			// 	'post_title'     => 'untitled survey',
			// 	'post_privacy'   => 'public',
			// 	'post_type'      => 'survey',
			// 	'post_survey'    => null,
			// 	'post_gender'    => 'survey',
			// 	'post_status'    => 'draft',
			// 	'post_sarshomar' => $this->post_sarshomar
			// ];
			// if(!$this->update_mode)
			// {
			// 	$survey_id = \lib\db\polls::insert($args);
			// 	// insert the poll
			// 	$insert_poll = $this->insert_poll(['survey_id' => $survey_id]);
			// 	// redirect to @/$url/add to add another poll
			// 	$url = \lib\utility\shortURL::encode($survey_id);
			// 	if($insert_poll)
			// 	{
			// 		// set dashboard data
			// 		\lib\utility\profiles::set_dashboard_data($this->login('id'), 'my_survey');
			// 		$this->redirector()->set_url("@/add/$url");
			// 	}
			// }
		}
		elseif(utility::post("add_poll"))
		{
			//users click on this buttom
			// change type of the poll of this suervey to 'survey_poll_[polltype - media - image , text,  ... ]'
			// $poll_type   = "survey_poll_"; // need to check
			// // get the survey id and survey url
			// $survey_id   = $this->check_poll_url($_args, "decode");
			// $survey_url  = $this->check_poll_url($_args, "encode");
			// // insert the poll
			// $insert_poll = $this->insert_poll(['poll_type' => $poll_type, 'survey_id' => $survey_id]);
			// // save survey title
			// $this->set_suervey_title($survey_id);
			// // redirect to '@/survey id /add' to add another poll
			// if($insert_poll)
			// {
			// 	$this->redirector()->set_url("@/add/$survey_url");
			// }
		}
		else
		{
			// the user click on buttom was not support us !!
			debug::error(T_("Command not found"));
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
}
?>