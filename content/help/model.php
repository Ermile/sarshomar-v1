<?php
namespace content\help;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{
	use \content_api\v1\helpcenter\search\tools\search;

	public function post_search($_args)
	{
		$search = utility::post("search");
		\lib\utility::$REQUEST = new \lib\utility\request(
		[
			'method'  => 'array',
			'request' => ['search'  => $search]
		]);
		$result = $this->helpcenter_search();
		debug::result($result);

	}
}