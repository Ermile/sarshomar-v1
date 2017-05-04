<?php
namespace content_election\admin\election;
use \lib\utility;
use \lib\debug;

class model extends \content_election\main\model
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
		return \content_election\lib\elections::search();
	}


	/**
	 * Gets the election.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_election($_args)
	{
		$result = \content_election\lib\elections::get($this->getid($_args));
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
			'title'       => utility::post('title'),
			'status'      => utility::post('status'),
			'eligible'    => utility::post('eligible'),
			'voted'       => utility::post('voted'),
			'invalid'     => utility::post('invalid'),
			'cash'        => utility::post('cash'),
			'start_time'  => utility::post('start_time'),
			'end_time'    => utility::post('end_time'),
			'jalali_year' => utility::post('jalali_year'),
			'year'        => utility::post('year'),
			'desc'        => utility::post('desc'),
		];

		$result = \content_election\lib\elections::update($update, $id);
		if($result)
		{
			debug::true(T_("Election updated"));
		}
		else
		{
			debug::error(T_("Error in update election"));
		}

	}

	/**
	 * Posts an election.
	 * add a alection
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_election($_args)
	{
		$args =
		[
			'title'       => utility::post('title'),
			'status'      => utility::post('status'),
			'eligible'    => utility::post('eligible'),
			'voted'       => utility::post('voted'),
			'invalid'     => utility::post('invalid'),
			'cash'        => utility::post('cash'),
			'start_time'  => utility::post('start_time'),
			'end_time'    => utility::post('end_time'),
			'jalali_year' => utility::post('jalali_year'),
			'year'        => utility::post('year'),
			'desc'        => utility::post('desc'),
		];

		$result = \content_election\lib\elections::insert($args);
		if($result)
		{
			debug::true(T_("Election added"));
		}
		else
		{
			debug::error(T_("Error in adding election"));
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