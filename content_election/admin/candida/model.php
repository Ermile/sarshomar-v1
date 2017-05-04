<?php
namespace content_election\admin\candida;
use \lib\utility;
use \lib\debug;

class model extends \content_election\main\model
{


	/**
	 * Gets the identifier from url
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  The identifier.
	 */
	public function getid($_args)
	{
		$id = isset($_args->match->url[0][1]) ? $_args->match->url[0][1] : false;
		if(!$id)
		{
			return false;
		}
		return $id;
	}


	/**
	 * Gets the list.
	 *
	 * @return     <type>  The list.
	 */
	public function get_list()
	{
		return \content_election\lib\candidas::search();
	}


	/**
	 * Gets the candida.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_candida($_args)
	{
		$result = \content_election\lib\candidas::get($this->getid($_args));
		return $result;
	}


	public function post_edit($_args)
	{
		$id = $this->getid($_args);
		if(!$id)
		{
			return false;
		}
		$file_url   = null;
		$file_url_2 = null;

		if(utility::files('file_url'))
		{
			$target_dir = root. "public_html/static/images/election/";
			if(!\lib\utility\file::exists($target_dir))
			{
				\lib\utility\file::makeDir($target_dir);
			}

			$basename = basename(utility::files('file_url')["name"]);

			$target_file = $target_dir . $basename;

			if (move_uploaded_file(utility::files('file_url')["tmp_name"], $target_file))
			{
				$file_url = 'images/election/'. $basename;
			}
		}

		if(utility::files('file_url_2'))
		{
			$target_dir = root. "public_html/static/images/election/";

			if(!\lib\utility\file::exists($target_dir))
			{
				\lib\utility\file::makeDir($target_dir);
			}

			$basename = basename(utility::files('file_url_2')["name"]);
			$target_file = $target_dir . $basename;

			if (move_uploaded_file(utility::files('file_url_2')["tmp_name"], $target_file))
			{
				$file_url_2 = 'images/election/'. $basename;
			}

		}
		$update =
		[
			'name'         => utility::post('name'),
			'family'       => utility::post('family'),
			'father'       => utility::post('father'),
			'birthdate'    => utility::post('birthdate'),
			'nationalcode' => utility::post('nationalcode'),
			'electioncode' => utility::post('electioncode'),
			'election_id'  => utility::post('election_id'),
			'status'       => utility::post('status'),
			'desc'         => utility::post('desc'),
		];

		if($file_url)
		{
			$update['file_url'] = $file_url;
		}
		if($file_url_2)
		{
			$update['file_url_2'] = $file_url_2;
		}

		$result = \content_election\lib\candidas::update($update, $id);
		if($result)
		{
			debug::true(T_("candida updated"));
		}
		else
		{
			debug::error(T_("Error in update candida"));
		}

	}

	/**
	 * Posts an candida.
	 * add a alection
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_candida($_args)
	{
		$file_url   = null;
		$file_url_2 = null;

		if(utility::files('file_url'))
		{
			$target_dir = root. "public_html/static/images/election/";
			if(!\lib\utility\file::exists($target_dir))
			{
				\lib\utility\file::makeDir($target_dir);
			}

			$basename = basename(utility::files('file_url')["name"]);

			$target_file = $target_dir . $basename;

			if (move_uploaded_file(utility::files('file_url')["tmp_name"], $target_file))
			{
				$file_url = 'images/election/'. $basename;
			}
		}

		if(utility::files('file_url_2'))
		{
			$target_dir = root. "public_html/static/images/election/";

			if(!\lib\utility\file::exists($target_dir))
			{
				\lib\utility\file::makeDir($target_dir);
			}

			$basename = basename(utility::files('file_url_2')["name"]);
			$target_file = $target_dir . $basename;

			if (move_uploaded_file(utility::files('file_url_2')["tmp_name"], $target_file))
			{
				$file_url_2 = 'images/election/'. $basename;
			}

		}

		$args =
		[
			'name'         => utility::post('name'),
			'family'       => utility::post('family'),
			'father'       => utility::post('father'),
			'birthdate'    => utility::post('birthdate'),
			'nationalcode' => utility::post('nationalcode'),
			'electioncode' => utility::post('electioncode'),
			'election_id'  => utility::post('election_id'),
			'status'       => utility::post('status'),
			'desc'         => utility::post('desc'),
		];
		if($file_url)
		{
			$args['file_url'] = $file_url;
		}
		if($file_url_2)
		{
			$args['file_url_2'] = $file_url_2;
		}

		if(!is_numeric($args['election_id']) || !$args['election_id'])
		{
			debug::error(T_("Please select one items of election"));
			return false;
		}
		$result = \content_election\lib\candidas::insert($args);
		if($result)
		{
			debug::true(T_("candida added"));
		}
		else
		{
			debug::error(T_("Error in adding candida"));
		}

		// id
		// title
		// status
		// eligible
		// voted
		// invalid
		// cash
		// start_time
		// end_time
		// jalali_year
		// year
		// createdate
		// date_modified
		// desc
		// meta

	}

}
?>