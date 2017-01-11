<?php
namespace content\knowledge;
use \lib\utility;

class model extends \mvc\model
{

	public function post_search()
	{

		if(utility::post("type") == 'favourites')
		{
			if($this->login())
			{
				\lib\db\polls::favo_like("favourites", $this->login('id'), utility::post('id'));
			}
			return;
		}
		$field = [];
		if($this->login())
		{
			// to get favourites posts
			$field = ['login' => $this->login('id')];
		}

		return \lib\db\polls::search(utility::post("search"), $field);
	}

	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	use \content_api\search\tools\search;
	use \content_api\home\tools\ready;

	public function get_search($_args)
	{	
		$search = null;
		if(isset($_args->get("search")[0]))
		{
			$search = $_args->get("search")[0];
		}

		$user_id = $this->login('id');

		return $this->search($_args);
	}


	/**
	 * check the url and make real name from posts table
	 *
	 * @param      <type>  $_url_arg  The url argument
	 * @param      string  $_value    The value
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	function db_field_name($_url_arg, $_value)
	{
		$field = null;
		$value = $_value;
		switch ($_url_arg)
		{
			case 'pollcat':
				if($_value == "sarshomar")
				{
					$field = "post_sarshomar" ;
					$value = 1;
				}
				else
				{
					$field = "post_sarshomar" ;
					$value = null;
				}
				break;

			case 'pollgender':
				$field = "post_gender";
				break;

			case 'polltype':
				$field = "post_type";
				// $value = \content_u\add\model::change_type($_value);
				break;

			case 'status':
				$field = "post_status";
				break;

			default:
				$field = false;
				break;
		}
		return [$field , $value];
	}
}
?>