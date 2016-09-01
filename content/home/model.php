<?php
namespace content\home;
use \lib\debug;

class model extends \mvc\model
{
	public function get_test($object)
	{
		return 1;
	}

	public function post_random_result() {
		$query = "
				SELECT
					id
				FROM
					posts
				WHERE
					post_type LIKE 'poll%' AND
					post_status = 'publish'
					";
		$get_id = array_column(\lib\db\posts::select($query, "get"), "id");
		$random_key = array_rand($get_id);
		$result = \lib\db\polls::get_result($get_id[$random_key]);

		\lib\debug::msg("data", $result);

		$this->_processor(array("force_json" => true, "force_stop" => true));


	}

	public function put_test($object)
	{
		return 3;
	}

	public function delete_test($object)
	{
		return 4;
	}

}
?>