<?php
namespace content_election\home;

class model extends \content_election\main\model
{
	/**
	 * check url exist
	 *
	 * @param      <type>  $_url   The url
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function check_url($_url)
	{
		return \content_election\lib\elections::check_url($_url);
	}


	/**
	 * Gets the load.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_load($_args)
	{
		$election_id = $this->check_url(\lib\router::get_url());
		if($election_id)
		{
			$result             = [];
			$election           = \content_election\lib\elections::get($election_id);
			$result['election'] = $election;
			$vote               = \content_election\lib\results::get_last($election_id);
			$voted              = 0;

			if(isset($election['voted']))
			{
				$voted += (int) $election['voted'];
			}

			if(isset($election['invalid']))
			{
				$voted += (int) $election['invalid'];
			}

			if(!$voted)
			{
				$voted = 1;
			}

			foreach ($vote as $key => $value)
			{
				$vote[$key]['percent'] = round((intval($value['total']) * 100) / $voted, 3);
			}
			$result['result']  = $vote;
			$senario           = \content_election\lib\results::get_senario($election_id);
			$temp_senario = [];
			foreach ($senario as $key => $value)
			{
				$temp_senario[] =
				[
					// 'id'            => (isset($value['id'])) ? $value['id'] : null,
					// 'election_id'   => (isset($value['election_id'])) ? $value['election_id'] : null,
					// 'report_id'     => (isset($value['report_id'])) ? $value['report_id'] : null,
					// 'candida_id'    => (isset($value['candida_id'])) ? $value['candida_id'] : null,
					'total'         => (isset($value['total'])) ? $value['total'] : null,
					// 'status'        => (isset($value['status'])) ? $value['status'] : null,
					// 'createdate'    => (isset($value['createdate'])) ? $value['createdate'] : null,
					// 'date_modified' => (isset($value['date_modified'])) ? $value['date_modified'] : null,
					// 'desc'          => (isset($value['desc'])) ? $value['desc'] : null,
					// 'meta'          => (isset($value['meta'])) ? $value['meta'] : null,
					'name'          => (isset($value['name'])) ? $value['name'] : null,
					'family'        => (isset($value['family'])) ? $value['family'] : null,
					'father'        => (isset($value['father'])) ? $value['father'] : null,
					'nationalcode'  => (isset($value['nationalcode'])) ? $value['nationalcode'] : null,
					'birthdate'     => (isset($value['birthdate'])) ? $value['birthdate'] : null,
					'electioncode'  => (isset($value['electioncode'])) ? $value['electioncode'] : null,
					'file_url'      => (isset($value['file_url'])) ? $value['file_url'] : null,
					'file_url_2'    => (isset($value['file_url_2'])) ? $value['file_url_2'] : null,
					'date'          => (isset($value['date'])) ? $value['date'] : null,
					'level'         => (isset($value['level'])) ? $value['level'] : null,
					'number'        => (isset($value['number'])) ? $value['number'] : null,
					'cash'          => (isset($value['cash'])) ? $value['cash'] : null,
					'voted'         => (isset($value['voted'])) ? $value['voted'] : null,
					'invalid'       => (isset($value['invalid'])) ? $value['invalid'] : null,
				];
			}

			$result['senario'] = json_encode($temp_senario, JSON_UNESCAPED_UNICODE);


			$time_line = \content_election\lib\results::get_time_line($election_id);
			$result['time_line'] = $time_line;

			return $result;
		}
	}
}
?>