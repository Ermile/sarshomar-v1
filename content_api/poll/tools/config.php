<?php 
namespace content_api\poll\tools;
use \lib\utility;

trait config
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
		//	check user id
		if(!$this->login("id"))
		{
			return \lib\debug::error(T_("Please login to insert a poll"));
		}
			
		/**
		 * update the poll or survey
		 */
		$options['update'] = false;

		if(utility::request("id"))
		{
			if(preg_match("/^[". $this->shortURL. "]+$/", utility::request("id")))
			{
				$options['update'] = utility::request("id");
			}
			else
			{
				return \lib\debug::error(T_("Invalid parametr id"), 'id', 'arguments');
			}
		}

		$insert_poll = $this->insert_poll($options);

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

		$args['permission_sarshomar']   = $this->access('u', 'sarshomar_knowledge', 'add');
		
		$args['permission_profile']     = $this->access('u', 'complete_profile', 'admin');

		$args['shortURL']    			= $this->shortURL;

		return \lib\db\polls::create($args);
	}
}
?>