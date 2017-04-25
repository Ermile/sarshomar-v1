<?php
namespace content\election;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{

	public function get_election($_args)
	{

		// $result = $this->get_file('tZ9Y');
		// $result = $this->get_file('tZ7v'); // dev
		// return $result;
	}


	/**
	 * Posts an election.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_election($_args)
	{
		$result = $this->get_file('tZ9Y');
		// $result = $this->get_file('tZ7v'); // dev
		debug::msg('chart', $result);
	}

	/**
	 * Gets the file.
	 *
	 * @param      string  $_filename  The filename
	 *
	 * @return     <type>  The file.
	 */
	public function get_file($_filename)
	{
		$result = null;
		$url = root. 'public_html/files/';
		if(!\lib\utility\file::exists($url))
		{
			\lib\utility\file::makeDir($url);
		}

		$url .= 'charts/';
		if(!\lib\utility\file::exists($url))
		{
			\lib\utility\file::makeDir($url);
		}

		$url .=  $_filename . '.json';
		if(!\lib\utility\file::exists($url))
		{
			$result = $this->make_query_result($_filename);
			\lib\utility\file::write($url, $result);
		}
		else
		{
			$file_time = \filemtime($url);
			if((time() - $file_time) >  (60))
			{
				$result = $this->make_query_result($_filename);
				\lib\utility\file::write($url, $result);
			}
			else
			{
				$result = \lib\utility\file::read($url);
			}

		}
		return $result;
	}


	/**
	 * Makes a query result.
	 *
	 * @param      <type>   $_filename  The filename
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function make_query_result($_filename)
	{
		$post_id = \lib\utility\shortURL::decode($_filename);

		if(!$post_id)
		{
			return false;
		}
		$query =
		"
			SELECT
				count(*) AS `count`,
				DATE(polldetails.insertdate) AS `date`,
				polldetails.opt AS `opt`
			FROM
				polldetails
			WHERE
				polldetails.post_id = $post_id AND
				polldetails.opt <> 0 AND
				polldetails.status = 'enable'
			GROUP BY polldetails.opt, DATE(polldetails.insertdate)
		";
		$result = \lib\db::get($query);
		if(\lib\define::get_language() === 'fa')
		{
			foreach ($result as $key => $value)
			{
				$result[$key]['date'] = \lib\utility\jdate::date("Y-m-d", $value['date'], false);
			}
		}
		$result = json_encode($result, JSON_UNESCAPED_UNICODE);
		return $result;
	}
}