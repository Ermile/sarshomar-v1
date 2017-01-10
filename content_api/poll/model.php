<?php
namespace content_api\poll;
use \lib\utility;

class model extends \content_api\home\model
{
	
	/**
	 * delete a poll
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function delete_poll($_args)
	{
		return "delete";
	}


	/**
	 * Gets the poll.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The poll.
	 */
	public function get_poll($_args)
	{
		$result  = [];
		
		$poll_id = utility::request("id");
		
		if(!$poll_id)
		{
			\lib\debug::error(T_("poll id not found"));
		}

		$poll    = \lib\db\polls::get_poll($poll_id);

		$options = 
		[
			'get_filter' => true,
			'get_opts'   => true,
		];
		$result = $this->ready_poll($poll, $options);

		return $result;
	}


	/**
	 * Posts a poll.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_poll($_args)
	{		
		return $this->add($_args);
	}


	/**
	 * Puts a poll.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function put_poll($_args)
	{
		return $this->add($_args);
	}


	/**
	 * **********************************************internal function *********************************************************
	 */

	/**
	 * add a post
	 *
	 * @param      <type>   $_args     The arguments
	 * @param      array    $_options  The options
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function add($_args)
	{
		//	check user id
		if(!$this->login("id"))
		{
			return \lib\debug::error(T_("Please login to insert a poll"));
		}
			
		/**
		 * update the poll or survey
		 */
		$update = false;

		if(utility::request("id"))
		{
			if(preg_match("/^[". $this->shortURL. "]+$/", utility::request("id")))
			{
				$update = utility::request("id");
			}
			else
			{
				return \lib\debug::error(T_("Invalid parametr id"), 'id', 'arguments');
			}
		}

		// insert args
		$args                         = [];
		$args['user']                 = $this->login('id');		
		$args['title']                = utility::request("title");		
		$args['answers']              = utility::request("answers");		
		$args['type']                 = utility::request("type");	
		$args['options']              = utility::request("options");
		$args['filters']              = utility::request("filters");		
		$args['update']               = $update;
		$args['shortURL']             = $this->shortURL;
		$args['permission_sarshomar'] = $this->access('u', 'sarshomar_knowledge', 'add');		
		$args['permission_profile']   = $this->access('u', 'complete_profile', 'admin');

		if(utility::files("poll_file"))
		{
			$args['upload_name']      = "poll_file";
		}
		elseif (utility::request("file_path")) 
		{
			$args['file_path']        = utility::request("file_path");
		}

		return \lib\db\polls::create($args);
	}
}
?>