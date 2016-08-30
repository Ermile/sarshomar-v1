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
		$a = [1,2,4,9,15,159,952];
		$random_key = array_rand($a);

		var_dump(\lib\db\polls::getResult());
		var_dump(1);exit();
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