<?php
namespace content_election\data\result;
use \lib\utility;
use \lib\debug;
use \lib\utility\location;
use \lib\utility\location\countres;
use \lib\utility\location\cites;
use \lib\utility\location\provinces;

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
		if(!$this->countres)
		{
			$this->countres = 'iran';
		}


		if(!$this->provinces)
		{
			$country_id    = countres::get('name', $this->countres, 'id');
			$province_list = provinces::search(['country_id' => $country_id]);
			$temp          = $province_list;
			$name          = array_column($temp, 'name', 'id');
			$localname     = array_column($temp, 'localname', 'id');
			foreach ($name as $key => $value)
			{
				if(isset($localname[$key]))
				{
					$name[$key] = "$value - $localname[$key]";
				}
			}
			return $name;
		}
		else
		{
			$province_id = provinces::get('name', $this->provinces, 'id');
			$city_list   = cites::search(['province_id' => $province_id]);
			$temp        = $city_list;
			$name        = array_column($temp, 'name', 'id');
			$localname   = array_column($temp, 'localname', 'id');
			foreach ($name as $key => $value)
			{
				if(isset($localname[$key]))
				{
					$name[$key] = "$value - $localname[$key]";
				}
			}
			return $name;
		}
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
			if(countres::check($url[4]))
			{
				$this->countres = $url[4];
			}
		}

		if(isset($url[5]))
		{
			if(provinces::check($url[5]))
			{
				$this->provinces = $url[5];
			}
		}


		if(isset($url[6]))
		{
			if(cites::check($url[6]))
			{
				$this->cites = $url[6];
			}
		}

		return
		[
			'country'  => $this->countres,
			'province' => $this->provinces,
			'city'     => $this->cites
		];
	}


	/**
	 * Posts a save city.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function post_save_city($_args)
	{
		$election_id = $this->getid($_args);
		$location    = $this->find_location_url();
		$post        = utility::post();
		$location    = null;

		if($this->countres)
		{
			$location = 'province';
		}

		if($this->provinces)
		{
			$location = 'city';
		}

		if($this->cites)
		{
			$location = 'city';
		}

		if(!$location)
		{
			return false;
		}

		$insert = [];

		foreach ($post as $key => $value)
		{
			if(preg_match("/^total\_(\d+)\_(\d+)$/", $key, $split))
			{
				if(isset($split[1]) && isset($split[2]))
				{
					if($plase)
					{
						$insert[] =
						[
							'election_id'   => $election_id,
							'location_type' => $location,
							'candida_id'    => $split[2],
							'place'         => $split[1],
							'total'         => $value,
						];
					}
				}
			}
		}

		if(!empty($insert))
		{
			foreach ($insert as $key => $value)
			{
				$temp_total = $value['total'];
				unset($value['total']);
				if(!$id = \content_election\lib\resultbyplaces::check($value))
				{
					$value['total'] = $temp_total;
					\content_election\lib\resultbyplaces::insert($value);
				}
				else
				{
					\content_election\lib\resultbyplaces::update(['total' => $temp_total], $id);
				}
			}
		}

		if(\lib\debug::$status)
		{
			debug::true(T_("Result added"));
		}
		else
		{
			debug::error(T_("Error in adding result"));
		}
	}
}
?>