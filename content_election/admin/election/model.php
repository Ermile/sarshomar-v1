<?php
namespace content_election\admin\election;
use \lib\utility;
use \lib\debug;

class model extends \content_election\main\model
{

	/**
	 * Gets the posts data
	 *
	 * @return     array  The posts.
	 */
	public function getPosts()
	{
		$args =
		[
			'title'                   => utility::post('title'),
			'en_title'                => utility::post('en_title'),
			'status'                  => utility::post('status'),
			'eligible'                => utility::post('eligible'),
			'voted'                   => utility::post('voted'),
			'invalid'                 => utility::post('invalid'),
			'cash'                    => utility::post('cash'),
			'branchs'                 => utility::post('branchs'),
			'first_vote_male_count'   => utility::post('first_vote_male_count'),
			'first_vote_female_count' => utility::post('first_vote_female_count'),
			'signuped_count'          => utility::post('signuped_count'),
			'verified_count'          => utility::post('verified_count'),
			'candida_count'           => utility::post('candida_count'),
			'start_time'              => date("Y-m-d H:i:s", strtotime(utility::post('start_time'))),
			'end_time'                => date("Y-m-d H:i:s", strtotime(utility::post('end_time'))),
			'election_date'           => utility::post('election_date'),
			'jalali_year'             => utility::post('jalali_year'),
			'year'                    => utility::post('year'),
			'en_url'                  => utility::post('en_url'),
			'fa_url'                  => utility::post('fa_url'),
			'cat'                     => utility::post('cat'),
			'win'                     => utility::post('win'),
			'desc'                    => utility::post('desc'),
		];

		if(!utility::post('start_time'))
		{
			unset($args['start_time']);
		}

		if(!utility::post('end_time'))
		{
			unset($args['end_time']);
		}

		if(!utility::post('election_date'))
		{
			unset($args['election_date']);
		}
		return $args;
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


	/**
	 * Posts an edit.
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function post_edit($_args)
	{
		$id = $this->getid($_args);
		if(!$id)
		{
			return false;
		}

		$result = \content_election\lib\elections::update($this->getPosts(), $id);
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
		$result = \content_election\lib\elections::insert($this->getPosts());
		if($result)
		{
			debug::true(T_("Election added"));
		}
		else
		{
			debug::error(T_("Error in adding election"));
		}
	}
}
?>