<?php
namespace content_u\profile;

class view extends \mvc\view
{
	public function view_profile($_args)
	{
		$this->data->profile = $_args->api_callback;
		// $profile_data =
		// [
			// 	'email' 		   => null,
			// 	'firstname'        => null,
			// 	'lastname'         => null,
			// 	'gender'           => ['male', 'female'],
			// 	'marrital'         => ['single', 'married'],
			// 	'birthyear'        => null,
			// 	'birthmonth'       => null,
			// 	'birthday'         => null,
			// 	'uilanguage'       => null,
			// 	'religion'         => null,

			// 	'graduation'       => ['illiterate', 'undergraduate', 'graduate'],
			// 	'educationtype'    => null, // only in iran [academic|howzeh]
			// 	'course'           => null,
			// 	'degree'           => ['under diploma', 'diploma', '2 year college', 'bachelor', 'master', 'phd', 'other'],
			// 	'howzeh'           => null,
			// 	'howzehdegree'     => null,
			// 	'howzehcourse'     => null,
			// 	'educationcity'    => null,

			// 	'employmentstatus' => ['employee', 'unemployed', 'retired'],
			// 	'industry'         => null,
			// 	'company'          => null,
			// 	'jobcity'          => null,
			// 	'jobtitle'         => null,

			// 	'country'          => null,
			// 	'province'         => null,
			// 	'city'             => null,
			// 	'village'          => null,
			// 	'housestatus'      => ['owner', 'tenant', 'homeless'],

			// 	'birthcountry'     => null,
			// 	'birthprovince'    => null,
			// 	'birthcity'        => null,

			// 	'marrital'         => ['single', 'married'],
			// 	'boychild'         => null,
			// 	'girlchild'        => null,

			// 	'internetusage'    => ['low', 'mid', 'high'],
			// 	'skills'           => null,
			// 	'languages'        => null,
			// 	'books'            => null,
			// 	'writers'          => null,
			// 	'films'            => null,
			// 	'actors'           => null,
			// 	'genre'            => null,
			// 	'musics'           => null,
			// 	'artists'          => null,
			// 	'sports'           => null,
			// 	'sportmans'        => null,
			// 	'habbits'          => null,
			// 	'devices'          => null,
		// ];
	}
}
?>