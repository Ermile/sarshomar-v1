<?php 
namespace content_api\poll\tools;
use \lib\utility;

trait config
{
	public $current_poll_id   = null;
	public $current_short_url = null;

	/**
	 * check short url and return the poll id
	 */
	public function check_poll_url($_args, $_type = "decode")
	{
		if(is_null($this->current_poll_id) || is_null($this->current_short_url))
		{
			if(isset($_args->match->url[0]) && is_array($_args->match->url[0]))
			{
				if(!isset($_args->match->url[0][1]))
				{
					return false;
				}

				$url     = $_args->match->url[0][1];
				$poll_id = \lib\utility\shortURL::decode($url);

				// check is my poll this id
				
				if(
					!\lib\db\polls::is_my_poll($poll_id, $this->login('id')) &&
					!$this->access('u', 'sarshomar_knowledge', 'admin'))
				{
					\lib\debug::error(T_("This is not your poll"));
					return false;
				}

				$this->current_short_url = $url;
				$this->current_poll_id = $poll_id;
			}
			else
			{
				// \lib\debug::error(T_("Poll id not found"));
				return false;
			}
		}

		if($_type == "decode")
		{
			return $this->current_poll_id;
		}
		else
		{
			return $this->current_short_url;
		}

	}

	/**
	 * add a post
	 *
	 * @param      <type>   $_args     The arguments
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function add($_args, $_options = [])
	{
		$default_options = ['update' => false];
		$_options        = array_merge($default_options, $_options);

		//	check user id
		if(!$this->login("id"))
		{
			\lib\debug::error(T_("Please login to insert a poll"));
			return false;
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

		$insert_poll = $this->insert_poll($_options);
		return $insert_poll;
	}


	/**
	 * insert poll
	 * get data from utility::request()
	 *
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	private function insert_poll($_options = [])
	{
		$default_options = ['update' => false];
		$_options        = array_merge($default_options, $_options);

		// insert args
		$args                           = [];
		$args['user']                   = $this->login('id');
		$args['title']                  = utility::request("title");
		$args['answers']                = utility::request("answers");
		
		if(utility::files("poll_file"))
		{
			$args['upload_name']        = "poll_file";
		}
		elseif (utility::request("file_path")) 
		{
			$args['file_path']          = utility::request("file_path");
		}
		
		$args['options']                = utility::request("options");
		$args['filters']                = utility::request("filters");
		
		$args['update']                 = $_options['update'];
		$args['permission_sarshomar']   = $this->sarshomar;
		$args['permission_profile']     = $this->access('u', 'complete_profile', 'admin');

		return \lib\db\polls::create($args);
	}
}
?>