<?php
namespace content_u\add\filter;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{
	function get_edit(){}

	/**
	*	get add filter
	*/
	function get_filter($_args)
	{

	}


	/**
	 * save filter
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public function post_filter($_args)
	{

		// get the poll or survey id
		$poll_id = $this->check_poll_url($_args);

		if(!$poll_id)
		{
			debug::error(T_("Poll id not found"));
			return false;
		}

		$html_filter =
		[
			'male'           => ['male', 'gender'],				// gender
			'female'         => ['female', 'gender'],			// gender
			'single'         => ['single', 'marrital'],			// marrital
			'married'        => ['married', 'marrital'],		// marrital
			'illiterate'     => ['on', 'graduation'],			// graduation
			'undergraduate'  => ['on', 'graduation'],			// graduation
			'graduate'       => ['on', 'graduation'],			// graduation
			'employee'       => ['on', 'employmentstatus'],		// employmentstatus
			'unemployed'     => ['on', 'employmentstatus'],		// employmentstatus
			'retired'        => ['on', 'employmentstatus'],		// employmentstatus
			'under_diploma'  => ['on', 'degree'],				// degree
			'diploma'        => ['on', 'degree'],				// degree
			'2_year_college' => ['on', 'degree'],				// degree
			'bachelor'       => ['on', 'degree'],				// degree
			'master'         => ['on', 'degree'],				// degree
			'phd'            => ['on', 'degree'],				// degree
			'other'          => ['on', 'degree'],				// degree
			'-13'            => ['on', 'range'],				// range
			'14-17'          => ['on', 'range'],				// range
			'18-24'          => ['on', 'range'],				// range
			'25-30'          => ['on', 'range'],				// range
			'31-44'          => ['on', 'range'],				// range
			'45-59'          => ['on', 'range'],				// range
			'60+'            => ['on', 'range'],				// range
		];

		$filters = [];
		foreach ($html_filter as $filter => $value)
		{
			if(utility::post($filter) === $value[0])
			{
				$filters[$value[1]][] = str_replace('_', ' ', $filter);
			}
		}

		// remove full insert filter
		// for example the user set male and female filter
		// we remove the gender filter
		$sum_money_filter = 0;
		$support_filter   = \lib\db\filters::support_filter();
		foreach ($filters as $key => $value)
		{
			if(isset($support_filter[$key]))
			{
				if($value == $support_filter[$key])
				{
					unset($filters[$key]);
				}
				else
				{
					$sum_money_filter += (int) \lib\db\filters::money_filter($key);
				}
			}
		}

		if(!empty($filters))
		{
			// get the count user by this filter
			$count = \lib\db\filters::count_user($filters);

			// the min member
			$min_member = 1;

			// check sarshomar knowledge add permission to show error count member
			if(!$this->access('u', 'sarshomar_knowledge', 'add') && intval($count) < $min_member)
			{
				debug::error(T_("Currently there are :max users available but minimum allowd users is 100, please remove some tags to expand statistical population",["max" => $count]));
				return false;
			}
		}

		/**
		 * set ranks
		 * plus (int) member in member field
		 */
		$member = (int) utility::post("rangepersons-max");
		$member_exist = (int) \lib\db\users::get_count("awaiting");
		if($member <= $member_exist)
		{
			\lib\db\ranks::plus($poll_id, "member", intval($member), ['replace' => true]);
		}
		else
		{
			debug::error(T_(":max user was found, low  the slide of members ",["max" => $member_exist]));
			return false;
		}

		/**
		 * insert the money filters in ranks table
		 */
		\lib\db\ranks::plus($poll_id, "filter", $sum_money_filter, ['replace' => true]);

		// remove exist filter saved of this poll
		\lib\db\postfilters::remove($poll_id);

		// ready to insert filters in options table
		$filter_ids = \lib\db\filters::insert($filters);

		// if filter id not found insert the filter record and get the last_insert_id
		if(!is_null($filter_ids))
		{
			$insert_filters = \lib\db\postfilters::set($poll_id, $filter_ids);
		}

		if(\lib\debug::$status)
		{
			$short_url = $this->check_poll_url($_args, "encode");
			\lib\debug::true(T_("Sucessfully added filters"));
			$this->redirector()->set_url($this->url('prefix'). "/add/$short_url/publish");
		}
		else
		{
			\lib\debug::error(T_("Error in inserting poll filters"));
		}
	}
}
?>