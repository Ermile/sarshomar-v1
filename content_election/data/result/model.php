<?php
namespace content_election\data\result;
use \lib\utility;
use \lib\debug;

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
			$this->countres = 111;
		}

		if(!$this->provinces)
		{
			$country_id    = $this->countres;
			$province_list = \lib\db\locations::get_child($country_id);
			$temp          = $province_list;
			$name          = array_column($temp, 'name', 'id');
			$localname     = array_column($temp, 'local_name', 'id');
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
			$province_id = $this->provinces;
			$city_list   = \lib\db\locations::get_child($province_id);
			$temp        = $city_list;
			$name        = array_column($temp, 'name', 'id');
			$localname   = array_column($temp, 'local_name', 'id');
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
			$provinces = \lib\db\locations::get_child($url[4]);
			$countres = \lib\db\locations::get_country($url[4]);
			if(isset($countres['id']))
			{
				$this->countres = $countres['id'];
			}
		}

		if(isset($url[5]))
		{
			$city = \lib\db\locations::get_child($url[5]);
			$provinces = \lib\db\locations::get_province($url[5]);
			if(isset($provinces['id']))
			{
				$this->provinces = $provinces['id'];
			}
		}

		$return = [];
		if($this->countres)
		{
			$return['countres'] = $this->countres;
		}

		if($this->provinces)
		{
			$return['provinces'] = $this->provinces;
		}

		return $return;
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

		$location = 'province';

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
					$insert[] =
					[
						'election_id'   => $election_id,
						'candida_id'    => $split[2],
						'place'         => $split[1],
						'total'         => $value,
					];
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


	/**
	 * Gets the election list.
	 */
	public function get_election_list()
	{
		return \content_election\lib\elections::search(null,['pagenation' => false, 'limit' => false]);
	}
}
?>