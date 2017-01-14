<?php
namespace content\knowledge;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * Posts a search.
	 * to set or unset fav
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_search()
	{
		if(utility::post("type") == 'favourites')
		{
			if($this->login())
			{
				$poll_id = utility\shortURL::decode(utility::post('id'));
				return \lib\db\polls::fav($this->login('id'), $poll_id, ['return_debug' => false]);
			}
		}
	}


	use \content_api\search\tools\search;
	use \content_api\home\tools\ready;
	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_search($_args)
	{
		$search = null;
		if(isset($_args->get("search")[0]))
		{
			$search = $_args->get("search")[0];
		}

		$my_poll = false;

		if(\lib\storage::get('rep') == 'u')
		{
			$my_poll = true;
		}

		\lib\utility::$REQUEST = new \lib\utility\request(
		[
			'method' => 'array',
			'request' =>
			[
				'search'  => $search,
				'my_poll' => $my_poll
			]
		]);

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