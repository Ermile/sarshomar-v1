<?php 
namespace content_api\search;
use \lib\utility;

class model extends \content_api\home\model
{
	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The search.
	 */
	public function get_search($_args)
	{
		return $this->post_search($_args);
	}

	/**
	 * Posts a search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_search($_args)
	{	
		$meta   = [];
		$search = null;

		if(utility::request("search"))
		{
			$search = utility::request("search");
		}
		else
		{
			$meta['get_last'] = true;	
		}

		if(utility::request("my_poll"))
		{
			$meta['my_poll'] = true;
		}

		if(utility::request("get_count"))
		{
			$meta['get_count'] = true;
		}

		if(utility::request("language") && \lib\utility\location\languages::check(utility::request("language")))
		{
			$meta['post_language'] = utility::request("language");
		}
	
		$meta['login'] = $this->login('id');
		$result        = \lib\db\polls::search($search, $meta);
		$tmp_result    = [];

		if(is_array($result))
		{			
			foreach ($result as $key => $value) 
			{
				$tmp_result[] = $this->ready_poll($value);		
			}
		}
		return $tmp_result;
	}
}
?>