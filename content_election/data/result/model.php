<?php
namespace content_election\data\result;
use \lib\utility;
use \lib\debug;
use \lib\utility\location;
class model extends \content_election\main\model
{
	public $countres  = null;
	public $provinces = null;
	public $cites     = null;

	/**
	 * Gets the add city.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_add_city($_args)
	{
		$election_id = $this->getid($_args);
		$location    = $this->find_location_url();

		var_dump($this->countres, $this->provinces, $this->cites);
		exit();
		$cites       = location\cites::list("id", 'localname');
		$provinces   = location\provinces::list("id", 'localname');
		$countres    = location\countres::list("id", 'name');
	}


	/**
	 * Gets the result.
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  The result.
	 */
	public function get_result($_args)
	{
		$id = $this->getid($_args);
		if($id)
		{
			$result = \content_election\lib\results::search(null, ['election_id' => $id]);
			return $result;
		}
	}


	/**
	 * { function_description }
	 */
	public function find_location_url()
	{
		$url       = \lib\router::get_url();
		$url       = \lib\utility\safe::safe($url);
		$url       = explode('/', $url);

		if(isset($url[4]))
		{
			if(location\countres::check($url[4]))
			{
				$this->countres = $url[4];
			}
		}

		if(isset($url[5]))
		{
			if(location\provinces::check($url[5]))
			{
				$this->provinces = $url[5];
			}
		}


		if(isset($url[6]))
		{
			if(location\cites::check($url[6]))
			{
				$this->cites = $url[6];
			}
		}
	}
}
?>