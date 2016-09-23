<?php
namespace content_u\lists;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * get list data to show
	 */
	public function get_list()
	{
		return \lib\db\polls::xget();
	}


	/**
	 * post data and update or insert list data
	 */
	public function post_list()
	{

	}
}
?>