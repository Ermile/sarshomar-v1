<?php
namespace content_election\data\result;
use \lib\utility\location;

class view extends \content_election\main\view
{
	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_add_city($_args)
	{
		$election_id          = $this->model()->getid($_args);
		$election             = \content_election\lib\elections::get($election_id);
		$this->data->election = $election;

		$candida              = \content_election\lib\candidas::search(null,['election_id' => $election_id, 'sort' => 'family', 'order' => 'asc']);
		$this->data->candida  = $candida;

		$city_list            = $_args->api_callback;

		$this->data->list     = $city_list;

		$find_location_url    = $this->model()->find_location_url();

		$location = null;
		if(isset($find_location_url['country']) && $find_location_url['country'])
		{
			$location = 'province';
		}

		if(isset($find_location_url['province']) && $find_location_url['province'])
		{
			$location = 'city';
		}

		if($location)
		{
			$url = \lib\router::get_url();
			$url = explode('/', $url);
			$url = end($url);

			switch ($location)
			{
				case 'country':
					$place = \lib\utility\location\countres::get('name', $url, 'id');
					break;

				case 'province':
					$place = \lib\utility\location\provinces::get('name', $url, 'id');
					break;

				case 'city':
					$place = \lib\utility\location\cites::get('name', $url, 'id');
					break;

				default:
					continue;
					break;
			}

			$saved_value = \content_election\lib\resultbyplaces::search(null,
			[
				'election_id'   => $election_id,
				'location_type' => $location,
				'place'         => $place,
			]);
			// var_dump($place, $saved_value);
			// exit();
		}

		// var_dump($find_location_url);
		// exit();

	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function view_result($_args)
	{
		$result = $_args->api_callback;
		$this->data->result = $result;
	}

}
?>