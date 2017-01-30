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


	use \content_api\v1\poll\tools\get;
	use \content_api\v1\home\tools\ready;


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

		$poll_id = \lib\router::get_url(1);

		$this->user_id = $this->login('id');
		$args =
		[
			'get_filter'         => true,
			'get_opts'           => true,
			'get_options'	     => true,
			'get_public_result'  => false,
		];
		utility::$REQUEST = new utility\request(
			[
				'method' => 'array',
				'request' =>
				[
					'id'   => $poll_id,
				]
			]
		);
		$result = $this->get($args);

		return $result;
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

		$data    = json_decode($data, true);
		$request = utility\safe::safe($data);

		$id = null;
		if($_args)
		{
			$id = (isset($_args->get("url")[0][1])) ? $_args->get("url")[0][1] : null;
		}

		$method  = ['method' => 'post'];

		if($id)
		{
			$request['id'] = $id;
			$method        = ['method' => 'put'];
		}

		utility::$REQUEST = new utility\request(
			[
				'method' => 'array',
				'request' => $request
			]
			);

		$this->user_id = $this->login('id');
		$this->debug   = false;
		return $this->add($method);
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
			$request = [];
			$request['type'] = utility::get("list");
			$request['search'] = utility::get("q");
			if(utility::get('parent') != '~')
			{
				$request['parent'] = utility::get('parent');
			}

			utility::$REQUEST = new utility\request(
				[
					'method'  => 'array',
					'request' => $request,
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