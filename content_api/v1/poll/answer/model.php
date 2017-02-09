<?php
namespace content_api\v1\poll\answer;
use \lib\utility;
use \lib\debug;

class model extends \content_api\v1\home\model
{

	use \content_api\v1\poll\tools\get;

	use tools\get;
	use tools\add;
	use tools\delete;

	/**
	 * Gets the options.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The options.
	 */
	public function get_answer($_args)
	{
		return $this->poll_answer_get();
	}


	/**
	 * Puts an answer.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function put_answer($_args)
	{
		return $this->poll_answer_add(['method' => 'put']);
	}


	/**
	 * Posts an answer.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_answer($_args)
	{
		return $this->poll_answer_add();
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function delete_answer($_args)
	{
		return $this->poll_answer_delete();
	}



}
?>