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

		if($type != 'sarshomar')
		{
			$search =
			[
				'user_id'    => $user_id,
				'post_title' => $title
			];
		}
		else
		{
			$search =
			[
				// 'post_type'  => 'sarshomar',
				'post_title' => $title
			];
		}
		$result = \lib\db\polls::xget($search);
		return $result;

	}
}
?>