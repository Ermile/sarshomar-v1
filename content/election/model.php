<?php
namespace content\election;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{
	/**
	 * update chart every $update_every time
	 *
	 * @var        integer
	 */
	public static $update_every = 60 * 1;


	/**
	 * Gets the election.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The election.
	 */
	public function get_election($_args)
	{
		if(utility::get("id") && is_string(utility::get("id")))
		{
			$id = utility::get("id");
		}
		else
		{
			if(Tld === 'dev')
			{
				$id = 'tZ7v'; // sport id
			}
			else
			{
				$id = 'tZ9Y'; // election id
			}
		}
		$result = $this->get_file($id);

		return $result;
	}


	/**
	 * Posts an election.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_election($_args)
	{
		if(utility::post("id") && is_string(utility::post("id")))
		{
			$id = utility::post("id");
		}
		else
		{
			if(Tld === 'dev')
			{
				$id = 'tZ7v'; // sport id
			}
			else
			{
				$id = 'tZ9Y'; // election id
			}
		}

		$result = $this->get_file($id);
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
			if((time() - $file_time) >  (self::$update_every))
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
				DATE(answerdetails.createdate) AS `date`,
				answerdetails.opt AS `opt`
			FROM
				answerdetails
			WHERE
				answerdetails.post_id = $post_id AND
				answerdetails.opt <> 0 AND
				( answerdetails.status = 'enable' OR answerdetails.status = 'disable')
			GROUP BY answerdetails.opt, DATE(answerdetails.createdate)
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