<?php
namespace mvc;

class model extends \lib\mvc\model
{

	/**
	 * get birthday and return age
	 *
	 * @param      <type>  $_brithday  The brithday
	 */
    public function get_age($_brithday)
    {
        if($_brithday == null)
        {
            return null;
        }

        $brith_year  = date("Y", strtotime($_brithday));
        $brith_month = date("m", strtotime($_brithday));
        $brith_day   = date("d", strtotime($_brithday));
        // to convert the jalali date to gregorian date
        if(intval($brith_year) > 1300 && intval($brith_year) < 1400)
        {
            list($brith_year, $brith_month, $brith_day) = \lib\utility\jdate::toGregorian($brith_year, $brith_month, $brith_day);
            if($brith_month < 10)
            {
                $brith_month = "0". $brith_month;
            }
            if($brith_day < 10)
            {
                $brith_day = "0". $brith_day;
            }
        }
        // get date diff
        $date1 = new \DateTime($brith_year. $brith_month. $brith_day);
        $date2 = new \DateTime("now");
        $age   = $date1->diff($date2);
        $age   = $age->y;
		return $age;
    }


    /**
     * Gets the range.
     *
     * @param      integer  $_age   The age
     *
     * @return     string   The range.
     */
    public function get_range($_age)
    {
        $range = null;

        $_age = intval($_age);

        switch ($_age) {
            case $_age < 18 :
                $range = "less than 18";
                break;

            case $_age >= 18 && $_age <= 24 :
                $range = "between 18 and 24";
                break;

            case $_age >= 25 && $_age <= 34 :
                $range = "between 25 and 34";
                break;

            case $_age >= 35 && $_age < 44 :
                $range = "between 35 and 44";
                break;

            case $_age >= 45 && $_age <= 54 :
                $range = "between 45 and 54";
                break;

            case $_age >= 55 && $_age <= 64 :
                $range = "between 55 and 64";
                break;

            case $_age >= 65:
                $range = "more than 65";
                break;
        }
        return $range;
    }
}
?>