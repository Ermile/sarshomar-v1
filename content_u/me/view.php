<?php
namespace content_u\me;

class view extends \mvc\view
{
	public function view_me($_args)
	{
		$this->data->me = $_args->api_callback;
		/**
		*  array [
			'id'            => '33'
			'gender'        => 'female'
			'marrital'      => 'single'
			'birthday'      => null
			'age'           => null
			'language'      => null
			'graduation'    => null
			'course'        => null
			'employment'    => null
			'business'      => null
			'industry'      => null
			'countrybirth'  => 'qom'
			'provincebirth' => 'qom'
			'citybirth'     => 'qom'
			'country'       => 'qom'
			'province'      => 'qom'
			'city'          => 'tehran'
			'parental'      => null
			'exercise'      => null
			'devices'       => null
			'internetusage' => null
			'displayname'   => 'تست'
			'email'         => 'Rmail@mae.com'
			]
		 */
	}
}
?>