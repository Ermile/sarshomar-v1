<?php
namespace content_election\home;
use \lib\utility;
use \lib\debug;

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
			$result               = [];
			$election             = \content_election\lib\elections::get($election_id);
			$result['election']   = $election;
			$vote                 = \content_election\lib\results::get_last($election_id);
			$candida_id           = \content_election\lib\candidas::list($election_id);

			$result['candida']    = $candida_id;
			$result['candida_id'] = array_column($candida_id, 'name_family', 'candida_id');

			$total = array_column($vote, 'total');
			$total = array_sum($total);
			if(!$total)
			{
				$total = 1;
			}
			foreach ($vote as $key => $value)
			{
				$vote[$key]['percent'] = round((intval($value['total']) * 100) / $total, 3);
			}
			$result['result'] = $vote;
			$senario          = \content_election\lib\results::get_senario($election_id);
			$temp_senario     = [];

			foreach ($senario as $key => $value)
			{
				// $xkey = $value['name'] . ' ' . $value['family']. '(' . $value['fame'] . ')';
				$xkey = $value['fame'];
				if(!$xkey)
				{
					$xkey = $value['family'];
				}

				if(isset($temp_senario[$value['report_id']]))
				{
					$temp_senario[$value['report_id']][$xkey] = (int)$value['total'];
					$temp_senario[$value['report_id']]['total'] += (int)$value['total'];
				}
				else
				{
					$temp_senario[$value['report_id']] =
					[
						// 'title' => $value['level'] . '_' . $value['number'] ,
						'title' => $value['level'],
						'date'  => (isset($value['date'])) ? $value['date'] : null,
						$xkey   =>  (int)$value['total'],
						'total' => $value['total'],
					];
				}

				// $temp_senario[] =
				// [
				// 	'total'         => (isset($value['total'])) ? $value['total'] : null,
				// 	'fame'          => (isset($value['fame'])) ? $value['fame'] : null,
				// 	'date'          => (isset($value['date'])) ? $value['date'] : null,
				// 	'level'         => (isset($value['level'])) ? $value['level'] : null,
				// 	'number'        => (isset($value['number'])) ? $value['number'] : null,
				// 	// 'id'            => (isset($value['id'])) ? $value['id'] : null,
				// 	// 'election_id'   => (isset($value['election_id'])) ? $value['election_id'] : null,
				// 	// 'report_id'     => (isset($value['report_id'])) ? $value['report_id'] : null,
				// 	// 'candida_id'    => (isset($value['candida_id'])) ? $value['candida_id'] : null,
				// 	// 'status'        => (isset($value['status'])) ? $value['status'] : null,
				// 	// 'createdate'    => (isset($value['createdate'])) ? $value['createdate'] : null,
				// 	// 'date_modified' => (isset($value['date_modified'])) ? $value['date_modified'] : null,
				// 	// 'desc'          => (isset($value['desc'])) ? $value['desc'] : null,
				// 	// 'meta'          => (isset($value['meta'])) ? $value['meta'] : null,
				// 	// 'family'        => (isset($value['family'])) ? $value['family'] : null,
				// 	// 'father'        => (isset($value['father'])) ? $value['father'] : null,
				// 	// 'nationalcode'  => (isset($value['nationalcode'])) ? $value['nationalcode'] : null,
				// 	// 'birthdate'     => (isset($value['birthdate'])) ? $value['birthdate'] : null,
				// 	// 'electioncode'  => (isset($value['electioncode'])) ? $value['electioncode'] : null,
				// 	// 'file_url'      => (isset($value['file_url'])) ? $value['file_url'] : null,
				// 	// 'file_url_2'    => (isset($value['file_url_2'])) ? $value['file_url_2'] : null,
				// 	// 'cash'          => (isset($value['cash'])) ? $value['cash'] : null,
				// 	// 'voted'         => (isset($value['voted'])) ? $value['voted'] : null,
				// 	// 'invalid'       => (isset($value['invalid'])) ? $value['invalid'] : null,
				// ];
			}

			// change to percentage
			$temp_senario = array_values($temp_senario);
			foreach ($temp_senario as $id => $report)
			{
				$myTotal = 0;
				if(isset($report['total']))
				{
					$myTotal = $report['total'];
				}

				foreach ($report as $key => $value)
				{
					switch ($key)
					{
						case 'total':
						case 'title':
						case 'date':
							// do nothing
							break;

						default:
							$temp_senario[$id][$key] = round($value * 100 / $myTotal , 2);
							break;
					}
				}
			}

			// var_dump($temp_senario);exit();
			if(count($temp_senario) > 1)
			{
				$result['senario'] = json_encode($temp_senario, JSON_UNESCAPED_UNICODE);

			}
			else
			{
				$result['senario'] = null;
			}

			$time_line = \content_election\lib\results::get_time_line($election_id);
			$result['time_line'] = $time_line;

			$result['result_by_city'] = \content_election\lib\resultbyplaces::get_election($election_id);
			if(isset($result['election']['status']) && $result['election']['status'] != 'awaiting')
			{
				if(isset($result['result'][0]['total']) && isset($result['result'][1]['total']) && $result['result'][1]['total'] === $result['result'][0]['total']  && $result['result'][1]['total'] === '0')
				{
					// no thing!
				}
				else
				{
					if(isset($result['result'][0]))
					{
						$result['rival'][0] = $result['result'][0];
					}

					if(isset($result['result'][1]))
					{
						$result['rival'][1] = $result['result'][1];
					}
				}
			}

			$result['comment'] = $this->get_comment($_args);
			// var_dump($result);exit();
			return $result;
		}

	}


	/**
	 * Gets the home.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The home.
	 */
	public function get_home($_args)
	{
		$time_line = \content_election\lib\results::home_page('president', true);
		return $time_line;
	}


	/**
	 * Gets the candida.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_candida($_args)
	{
		$cat = 'president';
		$result = \content_election\lib\candidas::get_list_all($cat);
		return $result;
	}


	/**
	 * Gets the comment.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_comment($_args)
	{
		$url         = (isset($_args->match->url[0][0])) ? $_args->match->url[0][0] : false;
		$election_id = false;

		if($url)
		{
			$url = str_replace('/comment', '', $url);
			$election_id = $this->check_url($url);
		}
		$query = "SELECT * FROM comments WHERE status = 'approved' ";
		$result = \lib\db::get($query, null, false, 'election');
		// var_dump($result);exit();
		return $result;
	}


	/**
	 * Posts a comment.
	 */
	public function post_comment($_args)
	{
		$name    = utility::post('name');
		$mobile  = utility::post('mobile');
		$comment = utility::post('comment');

		$log_meta =
		[
			'data' => null,
			'meta' =>
			[
				'input'   => utility::post(),
				'session' => $_SESSION,
			],
		];

		$url         = (isset($_args->match->url[0][0])) ? $_args->match->url[0][0] : false;
		$election_id = false;

		if($url)
		{
			$url = str_replace('/comment', '', $url);
			$election_id = $this->check_url($url);
		}

		if(!$election_id)
		{
			$election_id = 'NULL';
		}

		$user_id = 'NULL';
		$mobile  = \lib\utility\filter::mobile($mobile);

		if($mobile)
		{
			// check existing mobile
			$exists_user = \lib\db\users::get_by_mobile($mobile);
			// register if the mobile is valid
			if(!$exists_user || empty($exists_user))
			{
				// signup user by site_guest
				$user_id = \lib\db\users::signup(['ignore' => true, 'mobile' => $mobile ,'type' => 'inspection', 'port' => 'site_guest', 'displayname' => $name]);
				// save log by caller 'user:send:contact:register:by:mobile'
				\lib\db\logs::set('user:send:contact:register:by:mobile:in:election', $user_id, $log_meta);
			}
		}

		if(!$mobile)
		{
			$mobile = 'NULL';
		}

		if(mb_strlen($comment) > 1000)
		{
			\lib\db\logs::set('comment:in:election:too:large', $user_id, $log_meta);
			debug::error(T_("Text too large!"), 'comment');
			return false;
		}

		$query =
		"
		INSERT INTO comments
		SET
			comments.user_id = $user_id,
			comments.post_id = $election_id,
			comments.author  = '$name',
			comments.email   = NULL,
			comments.mobile  = '$mobile',
			comments.url     = NULL,
			comments.content = '$comment',
			comments.meta    = NULL
			-- comments.visitor_id =
		";
		$insert = \lib\db::query($query, 'election');
		if($insert)
		{
			\lib\db\logs::set('user:send:contact', $user_id, $log_meta);
			debug::true(T_("Thank You For contacting us"));
		}
		else
		{
			\lib\db\logs::set('user:send:contact:fail', $user_id, $log_meta);
			debug::error(T_("We could'nt save the contact"));
		}

	}
}
?>