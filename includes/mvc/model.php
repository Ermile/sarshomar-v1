<?php
namespace mvc;

class model extends \lib\mvc\model
{

	/**
	 * get birthday and return age
	 *
	 * @param      <type>  $_brithday  The brithday
	 */
    public static function get_age($_brithday)
    {
    	if(!\lib\utility\jdate::is_jalali($_brithday))
    	{
    		$brithday = \lib\utility\jdate::toGregorian($_brithday);
    	}
    	else
    	{
    		$brithday = explode("-", $_brithday);
    	}

		$age = date("Y") - $brithday[0];
		return $age;
    }
}
?>