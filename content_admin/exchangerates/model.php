<?php
namespace content_admin\exchangerates;

use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * get a record of exchangerates to edit
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
			$exchangerates = \lib\db\exchangerates::get($id);
			return $exchangerates;
		}
	}


	/**
	 * save a record of exchangerates
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
				'from'         => utility::post('from') 		? utility::post('from')			: null,
				'to'           => utility::post('to') 			? utility::post('to')			: null,
				'rate'         => utility::post('rate') 		? utility::post('rate')			: null,
				'roundtype'    => utility::post('roundtype') 	? utility::post('roundtype')	: null,
				'round'        => utility::post('round') 		? utility::post('round')		: null,
				'wagestatic'   => utility::post('wagestatic') 	? utility::post('wagestatic')	: null,
				'wage'         => utility::post('wage') 		? utility::post('wage')			: null,
				'status'       => utility::post('status') 		? utility::post('status')		: null,
				'desc'         => utility::post('desc') 		? utility::post('desc')			: null,
				'meta'         => utility::post('meta') 		? utility::post('meta')			: null,
				'createdate'   => utility::post('createdate') 	? utility::post('createdate')	: null,
				'datemodified' => utility::post('datemodified') ? utility::post('datemodified')	: null,
				'enddate'      => utility::post('enddate') 		? utility::post('enddate')		: null,
			];
			$update = \lib\db\exchangerates::update($args, $id);
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
	 * get all exchangerates items
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The add.
	 */
	public function get_add($_args)
	{

	}


	/**
	 * save a record of exchangerates
	 */
	public function post_add()
	{

		$args =
		[
			'from'         => utility::post('from') 		? utility::post('from')			: null,
			'to'           => utility::post('to') 			? utility::post('to')			: null,
			'rate'         => utility::post('rate') 		? utility::post('rate')			: null,
			'roundtype'    => utility::post('roundtype') 	? utility::post('roundtype')	: null,
			'round'        => utility::post('round') 		? utility::post('round')		: null,
			'wagestatic'   => utility::post('wagestatic') 	? utility::post('wagestatic')	: null,
			'wage'         => utility::post('wage') 		? utility::post('wage')			: null,
			'status'       => utility::post('status') 		? utility::post('status')		: null,
			'desc'         => utility::post('desc') 		? utility::post('desc')			: null,
			'meta'         => utility::post('meta') 		? utility::post('meta')			: null,
			'createdate'   => utility::post('createdate') 	? utility::post('createdate')	: null,
			'datemodified' => utility::post('datemodified') ? utility::post('datemodified')	: null,
			'enddate'      => utility::post('enddate') 		? utility::post('enddate')		: null,
		];
		$insert = \lib\db\exchangerates::insert($args);
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