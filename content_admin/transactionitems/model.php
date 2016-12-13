<?php
namespace content_admin\transactionitems;

use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * get a record of transactions to edit
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
			$transactionitems = \lib\db\transactionitems::get($id);
			return $transactionitems;
		}
	}


	/**
	 * save a record of transactions
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
				'title'       => utility::post("title"),
				'caller'      => utility::post("caller"),
				'unit_id'     => utility::post("unit_id"),
				'type'        => utility::post("type"),
				'minus'       => utility::post("minus"),
				'plus'        => utility::post("plus"),
				'autoverify'  => utility::post("autoverify"),
				'forcechange' => utility::post("forcechange"),
				'desc'        => utility::post("desc"),
				'meta'        => utility::post("meta"),
				'status'      => utility::post("status"),
				'enddate'     => utility::post("enddate")
			];
			$update = \lib\db\transactionitems::update($args, $id);
			if($update)
			{
				debug::true(T_("Transaction items update successfuly"));
			}
			else
			{
				debug::error(T_("Operation faild"));
			}
		}

	}


	/**
	 * get all transactions items
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The add.
	 */
	public function get_add($_args)
	{

	}


	/**
	 * save a record of transactions
	 */
	public function post_add()
	{
		$args =
		[
			'title'       => utility::post("title"),
			'caller'      => utility::post("caller"),
			'unit_id'     => utility::post("unit_id"),
			'type'        => utility::post("type"),
			'minus'       => utility::post("minus"),
			'plus'        => utility::post("plus"),
			'autoverify'  => utility::post("autoverify"),
			'forcechange' => utility::post("forcechange"),
			'desc'        => utility::post("desc"),
			'meta'        => utility::post("meta"),
			'status'      => utility::post("status"),
			'enddate'     => utility::post("enddate")
		];
		$insert = \lib\db\transactionitems::insert($args);
		if($insert)
		{
			debug::true(T_("Transaction items insert successfuly"));
		}
		else
		{
			debug::error(T_("Operation faild"));
		}
	}
}
?>