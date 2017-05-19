<?php
namespace content_election\data\report;
use \lib\utility;
use \lib\debug;

class model extends \content_election\main\model
{

	/**
	 * Gets the add.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     array   The add.
	 */
	public function get_add($_args)
	{
		$id                 = $this->getid($_args);
		$result             = [];
		$result['election'] = \content_election\lib\elections::search(null, ['id' => $id, 'limit' => 1]);

		if(isset($result['election'][0]))
		{
			$result['election'] = $result['election'][0];
		}

		$result['candida']  = \content_election\lib\candidas::search(null,
			['election_id' => $id, 'pagenation' => false, 'sort' => 'family', 'order' => 'asc']);
		$result['report']   = \content_election\lib\reports::search(null,
			['election_id' => $id, 'pagenation' => false, 'sort' => 'id', 'order' => 'asc']);

		return $result;
	}


	/**
	 * Gets the list.
	 *
	 * @return     <type>  The list.
	 */
	public function get_list()
	{
		return \content_election\lib\reports::search();
	}


	/**
	 * Posts an add result.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_add_result($_args)
	{
		$id                    = $this->getid($_args);

		if(!$id)
		{
			return false;
		}

		$report                = [];
		$report['election_id'] = $id;
		$report['date']        = utility::post('date');
		$report['level']       = utility::post('level');
		$report['number']      = utility::post('number');
		$report['cash']        = utility::post('cash');
		$report['voted']       = utility::post('voted');
		$report['invalid']     = utility::post('invalid');

		$candida_total    = [];

		foreach (utility::post() as $key => $value)
		{
			if(preg_match("/^total\_(\d+)$/", $key, $split))
			{
				if(isset($split[1]))
				{
					$candida_total[$split[1]] = $value;
				}
			}
		}

		$report_id = null;
		$temp      = $report;
		$temp      = array_filter($temp);

		if(!empty($temp))
		{
			$report_id = \content_election\lib\reports::insert($report);
		}

		$insert = [];
		if(!empty($candida_total))
		{
			foreach ($candida_total as $key => $value)
			{
				$insert[] =
				[
					'election_id' => $id,
					'report_id'   => $report_id,
					'candida_id'  => $key,
					'total'       => $value,
				];
			}
		}

		if(!empty($insert))
		{
			\content_election\lib\results::disable_old($id);
			$result = \content_election\lib\results::insert_multi($insert);
			if($result)
			{
				// \content_election\lib\results::update_cash($id);
				debug::true(T_("Result added"));
			}
			else
			{
				debug::error(T_("Error in adding result"));
			}
		}
	}


	/**
	 * Gets the report.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_report($_args)
	{
		$result = \content_election\lib\reports::get($this->getid($_args));
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

		$update =
		[
			'election_id' => utility::post('election_id'),
			'date'        => utility::post('date'),
			'status'      => utility::post('status'),
			'desc'        => utility::post('desc'),
			'level'       => utility::post('level'),
			'number'      => utility::post('number'),
			'cash'        => utility::post('cash'),
			'voted'       => utility::post('voted'),
			'invalid'     => utility::post('invalid'),
		];

		$result = \content_election\lib\reports::update($update, $id);
		if($result)
		{
			debug::true(T_("Report updated"));
		}
		else
		{
			debug::error(T_("Error in update report"));
		}
	}


	/**
	 * Posts an report.
	 * add a alection
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_report($_args)
	{

		$args =
		[
			'election_id' => utility::post('election_id'),
			'date'        => utility::post('date'),
			'status'      => utility::post('status'),
			'desc'        => utility::post('desc'),
			'level'       => utility::post('level'),
			'number'      => utility::post('number'),
			'cash'        => utility::post('cash'),
			'voted'       => utility::post('voted'),
			'invalid'     => utility::post('invalid'),
		];
		if(!is_numeric($args['election_id']) || !$args['election_id'])
		{
			debug::error(T_("Please select one items of election"));
			return false;
		}
		$result = \content_election\lib\reports::insert($args);
		if($result)
		{
			debug::true(T_("report added"));
		}
		else
		{
			debug::error(T_("Error in adding report"));
		}
	}
}
?>