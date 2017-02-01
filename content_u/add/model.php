<?php
namespace content_u\add;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{

	/**
	 * use api tools
	 */
	use \content_api\v1\poll\tools\add;

	use \content_api\v1\poll\tools\get;

	use \content_api\v1\home\tools\ready;

	use \content_api\v1\poll\opts\tools\get;

	use \content_api\v1\tag\search\tools\search;

	use \content_api\v1\poll\search\tools\search;


	/**
	 * Gets the add.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_add($_args)
	{
		if($this->local_search())
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
		if($this->local_search())
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
		$result = $this->poll_get($args);

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
		if($this->local_search())
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
		if($this->local_search())
		{
			return;
		}

		return $this->poll($_args);
	}



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
		return $this->poll_add($method);
	}


	/**
	 * search in terms list
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	private function local_search()
	{
		if(utility::get("list"))
		{

			$result = null;
			switch (utility::get("list"))
			{
				case 'cat':
				case 'tag':
				case 'profile':
					$result = $this->term_list();
					break;

				case 'tree':
					$result = $this->tree();
					break;

				case 'opts':
					$result = $this->opts();

				default:
					return false;
					break;
			}
			debug::msg("list", $result);
			return true;
		}
		return false;
	}


	/**
	 * get poll opts
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function opts()
	{
		utility::set_request_array(['id' => utility::get("id")]);
		$this->user_id = $this->login('id');
		$result        = $this->poll_opts();
		$tmp_result = [];

		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				if(isset($value['title']))
				{
					$tmp_result[] = ['value' => ++$key, 'title' => $value['title']];
				}
			}
		}
		return $result;
	}


	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function tree()
	{
		$search = utility::get("q");
		$my_poll = false;

		utility::set_request_array(
		[
			'search'  => $search,
			'my_poll' => $my_poll
		]);

		$this->user_id = $this->login('id');

		$result = $this->poll_search();
		$tmp_result = [];
		foreach ($result['data'] as $key => $value)
		{
			$tmp_result[$key]['title'] = isset($value['title']) ? $value['title'] : null;
			$tmp_result[$key]['desc']  = isset($value['summary']) ? $value['summary'] : null;
			$tmp_result[$key]['value'] = isset($value['id']) ? $value['id'] : null;
			$tmp_result[$key]['url']   = isset($value['url']) ? $value['url'] : null;
		}
		return json_encode($tmp_result);
	}


	/**
	 * search in terms
	 * cat
	 * tag
	 * profile
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function term_list()
	{
		$request = [];
		$request['type'] = utility::get("list");
		$request['search'] = utility::get("q");
		if(utility::get('parent') != '~')
		{
			$request['parent'] = utility::get('parent');
		}

		utility::set_request_array(
		[
			'method'  => 'array',
			'request' => $request,
		]);

		$result = $this->tag_search();
		return json_encode($result);
	}
}
?>