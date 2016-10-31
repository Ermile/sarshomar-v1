<?php
namespace content_u\me;

class view extends \mvc\view
{
	public function view_me($_args)
	{
		$this->data->me = $_args->api_callback;
		// $profile_data =
		// [
		// 	'firstname'        => null,
		// 	'lastname'         => null,
		// 	'gender'           => ['male', 'female'],
		// 	'marrital'         => ['single', 'married'],
		// 	'birthdate'        => null,
		// 	'birthyear'        => null,
		// 	'birthmonth'       => null,
		// 	'birthday'         => null,
		// 	'age'              => null,
		// 	'range'            => ['-13', '14-17', '18-24', '25-30', '31-44', '45-59', '60+'],
		// 	'rangetitle'       => ['baby', 'teenager', 'young', 'adult'],
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
		// 	'employmentstatus' => ['employee', 'unemployee', 'retired'],
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