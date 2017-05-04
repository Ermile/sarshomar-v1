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
			$result['result']   = $vote;

			return $result;
		}
	}
}
?>