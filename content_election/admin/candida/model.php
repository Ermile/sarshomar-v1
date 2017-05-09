<?php
namespace content_election\admin\candida;
use \lib\utility;
use \lib\debug;

class model extends \content_election\main\model
{

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
	 * get data in posts
	 *
	 * @return     array  The post.
	 */
	public function getPost()
	{
		$args =
		[
			'name'         => utility::post('name'),
			'en_name'      => utility::post('en_name'),
			'family'       => utility::post('family'),
			'en_family'    => utility::post('en_family'),
			'father'       => utility::post('father'),
			'en_father'    => utility::post('en_father'),
			'fame'         => utility::post('fame'),
			'en_fame'      => utility::post('en_fame'),
			'birthdate'    => utility::post('birthdate'),
			'nationalcode' => utility::post('nationalcode'),
			'electioncode' => utility::post('electioncode'),
			'election_id'  => utility::post('election_id'),
			'status'       => utility::post('status'),
			'desc'         => utility::post('desc'),
		];

		if(!utility::post('birthdate'))
		{
			unset($args['birthdate']);
		}

		$file_url = $this->find_updload('file_url');
		if($file_url)
		{
			$args['file_url'] = $file_url;
		}

		$file_url_2 = $this->find_updload('file_url_2');
		if($file_url_2)
		{
			$args['file_url_2'] = $file_url_2;
		}

		$win_url = $this->find_updload('win_url');
		if($win_url)
		{
			$args['win_url'] = $win_url;
		}

		return $args;
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


	/**
	 * add a candida
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

		$result = \content_election\lib\candidas::update($this->getPost(), $id);

		if($result)
		{
			debug::true(T_("Candida updated"));
		}
		else
		{
			debug::error(T_("Error in update candida"));
		}

	}


	/**
	 * find file uploaded or no
	 *
	 * @param      <type>  $_name  The name
	 *
	 * @return     string  ( description_of_the_return_value )
	 */
	public function find_updload($_name)
	{
		if(utility::files($_name))
		{
			$target_dir = root. "public_html/static/images/election/";
			if(!\lib\utility\file::exists($target_dir))
			{
				\lib\utility\file::makeDir($target_dir);
			}

			$basename = basename(utility::files($_name)["name"]);

			$target_file = $target_dir . $basename;

			if (move_uploaded_file(utility::files($_name)["tmp_name"], $target_file))
			{
				return 'images/election/'. $basename;
			}
		}
		return false;
	}


	/**
	 * Posts an candida.
	 * add a alection
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_candida($_args)
	{
		$args = $this->getPost();

		if(!is_numeric($args['election_id']) || !$args['election_id'])
		{
			debug::error(T_("Please select one items of election"));
			return false;
		}

		$result = \content_election\lib\candidas::insert($args);

		if($result)
		{
			debug::true(T_("Candida added"));
		}
		else
		{
			debug::error(T_("Error in adding candida"));
		}
	}
}
?>