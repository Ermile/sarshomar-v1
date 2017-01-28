<?php
namespace content_u\add;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{
	use filter\model;
	use publish\model;


	/**
	 * Gets the add.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_add($_args)
	{
		if($this->term_list())
		{
			return;
		}
	}


	/**
	 * Gets the edit.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_edit($_args)
	{
		if($this->term_list())
		{
			return;
		}
	}


	/**
	 * Posts an add.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_add($_args)
	{
		if($this->term_list())
		{
			return;
		}

		return $this->poll();
	}


	/**
	 * Posts an edit.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_edit($_args)
	{
		if($this->term_list())
		{
			return;
		}

		return $this->poll($_args);
	}


	/**
	 * use api add poll
	 */
	use \content_api\v1\poll\tools\add;

	/**
	 * insert or update poll
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function poll($_args = null)
	{
		$data = '{}';

		if(isset($_POST['data']))
		{
			$data = $_POST['data'];
		}
		$data = json_decode($data, true);

		$id = null;
		if($_args)
		{
			$id = (isset($_args->get("url")[0][1])) ? $_args->get("url")[0][1] : null;
		}

		$request = $data;

		if($id)
		{
			$request['id'] = $id;
		}

		utility::$REQUEST = new utility\request(
			[
				'method' => 'array',
				'request' => $request
			]
			);

		$this->user_id = $this->login('id');
		$this->debug   = false;
		return $this->add(['method' => 'put']);
	}



	/**
	 * use api teg search
	 */
	use \content_api\v1\tag\search\tools\search;


	/**
	 * search in terms list
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	private function term_list()
	{
		if(utility::get("list"))
		{
			utility::$REQUEST = new utility\request(
				[
					'method' => 'array',
					'request' =>
					[
						'type'   => utility::get("list"),
						'search' => utility::get("q")
					]
				]
			);
			$result = $this->search();
			$result = json_encode($result);
			debug::msg("list", $result);
			return true;
		}
		return false;
	}

}
?>