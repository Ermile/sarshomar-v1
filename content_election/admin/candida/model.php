<?php
namespace content_election\admin\candida;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{


	/**
	 * Gets the identifier from url
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  The identifier.
	 */
	public function getid($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : false;
		if(!$id)
		{
			return false;
		}
		return $id;
	}


	/**
	 * Gets the list.
	 *
	 * @return     <type>  The list.
	 */
	public function get_list()
	{
		return \content_election\lib\candidas::search();
	}


	/**
	 * Gets the candida.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_candida($_args)
	{
		$result = \content_election\lib\candidas::get($this->getid($_args));
		return $result;
	}


	public function post_edit($_args)
	{
		$id = $this->getid($_args);
		if(!$id)
		{
			return false;
		}

		$update =
		[
			'name'        => utility::post('name'),
			'election_id' => utility::post('election_id'),
			'status'      => utility::post('status'),
			'desc'        => utility::post('desc'),
		];

		$result = \content_election\lib\candidas::update($update, $id);
		if($result)
		{
			debug::true(T_("candida updated"));
		}
		else
		{
			debug::error(T_("Error in update candida"));
		}

	}

	/**
	 * Posts an candida.
	 * add a alection
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_candida($_args)
	{

		$args =
		[
			'name'        => utility::post('name'),
			'election_id' => utility::post('election_id'),
			'status'      => utility::post('status'),
			'desc'        => utility::post('desc'),
		];
		if(!is_numeric($args['election_id']) || !$args['election_id'])
		{
			debug::error(T_("Please select one items of election"));
			return false;
		}
		$result = \content_election\lib\candidas::insert($args);
		if($result)
		{
			debug::true(T_("candida added"));
		}
		else
		{
			debug::error(T_("Error in adding candida"));
		}

		// id
		// title
		// status
		// eligible
		// voted
		// invalid
		// cash
		// start_time
		// end_time
		// jalali_year
		// year
		// createdate
		// date_modified
		// desc
		// meta

	}

}
?>