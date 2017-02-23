<?php
namespace content_admin\units;

use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * get a record of units to edit
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_edit($_args)
	{
		$id = null;
		if(isset($_args->match->url[0][1]))
		{
			$id = $_args->match->url[0][1];
		}
		if($id)
		{
			$units = \lib\db\units::get($id);
			return $units;
		}
	}


	/**
	 * save a record of units
	 */
	public function post_edit($_args)
	{
		$id = null;
		if(isset($_args->match->url[0][1]))
		{
			$id = $_args->match->url[0][1];
		}
		if($id)
		{
			$args =
			[
				'title' => utility::post("title"),
				'desc'  => utility::post("desc"),
				'meta'  => utility::post("meta"),
			];
			$update = \lib\db\units::update($args, $id);
			if($update)
			{
				debug::true(T_("Unit items update successfuly"));
			}
			else
			{
				debug::error(T_("Operation faild"));
			}
		}

	}


	/**
	 * get all units items
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The add.
	 */
	public function get_add($_args)
	{

	}


	/**
	 * save a record of units
	 */
	public function post_add()
	{

		$args =
		[
			'title' => utility::post("title"),
			'desc'  => utility::post("desc"),
			'meta'  => utility::post("meta"),
		];
		$insert = \lib\db\units::insert($args);
		if($insert)
		{
			debug::true(T_("Unit items insert successfuly"));
		}
		else
		{
			debug::error(T_("Operation faild"));
		}
	}
}
?>