<?php
namespace content_api\poll\tools;
use \lib\utility;

trait add
{
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
		if(utility::request() == '' || is_null(utility::request()))
		{
			return \lib\debug::error(T_("Invalid input"), 'input', 'arguments');
		}

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