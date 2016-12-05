<?php
namespace content_u\add\filter;
use \lib\utility;
use \lib\debug;

class model extends \content_u\home\model
{

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
			debug::error(T_("poll id not found"));
			return false;
		}

		$html_filter =
		[
			'male'           => ['male', 'gender'],			// gender
			'female'         => ['female', 'gender'],		// gender
			'single'         => ['single', 'marrital'],		// marrital
			'marriade'       => ['marriade', 'marrital'],	// marrital
			'illiterate'     => ['on', 'graduation'],		// graduation
			'undergraduate'  => ['on', 'graduation'],		// graduation
			'graduate'       => ['on', 'graduation'],		// graduation
			'employee'       => ['on', 'employmentstatus'],	// employmentstatus
			'unemployee'     => ['on', 'employmentstatus'],	// employmentstatus
			'retired'        => ['on', 'employmentstatus'],	// employmentstatus
			'under_diploma'  => ['on', 'degree'],			// degree
			'diploma'        => ['on', 'degree'],			// degree
			'2_year_college' => ['on', 'degree'],			// degree
			'bachelor'       => ['on', 'degree'],			// degree
			'master'         => ['on', 'degree'],			// degree
			'phd'            => ['on', 'degree'],			// degree
			'other'          => ['on', 'degree']			// degree
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
		$support_filter = \lib\db\filters::support_filter();
		foreach ($filters as $key => $value)
		{
			if(isset($support_filter[$key]))
			{
				if($value == $support_filter[$key])
				{
					unset($filters[$key]);
				}
			}
		}

		// get the count user by this filter
		$count = \lib\db\filters::count_user($filters);

		// the min member
		$min_member = 1;

		// check sarshomar knowledge add permission to show error count member
		if(!$this->access('u', 'sarshomar_knowledge', 'add') && intval($count) < $min_member)
		{
			debug::error(T_(":max users found remove some filter",["max" => $count]));
			return false;
		}

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
			\lib\debug::true(T_("Add filter of poll Success"));
			$this->redirector()->set_url(\lib\define::get_language(). "/@/add/$short_url/publish");
		}
		else
		{
			\lib\debug::error(T_("Error in insert filter of poll"));
		}
	}
}
?>