<?php
namespace content_api\v1\poll\tools;
use \lib\utility;
use \lib\debug;

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
	public function add($_args, $_put = false)
	{
		if(!debug::$status)
		{
			return false;
		}

		if(utility::request() == '' || is_null(utility::request()))
		{
			return debug::error(T_("Invalid input"), 'input', 'arguments');
		}

		/**
		 * update the poll or survey
		 */
		$update = false;

		if($_put)
		{
			if(preg_match("/^[". \lib\utility\shortURL::ALPHABET. "]+$/", utility::request("id")))
			{
				$update = utility::request("id");
			}
			else
			{
				return debug::error(T_("Invalid parametr id"), 'id', 'arguments');
			}
		}
		elseif(utility::request("id"))
		{
			return debug::error(T_("Invalid parametr id"), 'id', 'arguments');
		}

		// insert args
		$args                         = [];
		$args['user']                 = $this->user_id;
		$args['title']                = utility::request("title");
		$args['answers']              = utility::request("answers");
		$args['type']                 = utility::request("type");
		$args['options']              = utility::request("options");
		$args['filters']              = utility::request("filters");
		$args['update']               = $update;
		$args['shortURL']             = \lib\utility\shortURL::ALPHABET;
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