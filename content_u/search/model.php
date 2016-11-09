<?php
namespace content_u\search;

class model extends \content_u\home\model
{

	/**
	 * set survey.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_search($_args)
	{
		$title = $_args->get("title");
		if(isset($title[0]))
		{
			$title = $title[0];
		}

		$type = $_args->get("type");
		if(isset($type[0]))
		{
			$type = $type[0];
		}

		$user_id = $this->login("id");
		$arg = [];
		if($type != 'sarshomar')
		{
			$arg = ['user_id' => $user_id];
		}

		$result = \lib\db\polls::search($title, $arg);
		return $result;

	}
}
?>