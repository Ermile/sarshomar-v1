<?php
namespace content\search;
use \lib\debug;

class model extends \mvc\model
{

	public function get_search($_args)
	{
		$search_in = false;
		$url = isset($_args->match->url[0][0]) ? $_args->match->url[0][0] : '';
		if(preg_match("/^search\/(\w+)$/", $url, $split))
		{
			if(isset($split[1]))
			{
				$search_in = $split[1];
			}
		}

		$search = \lib\utility::get("q");

		$result = [];
		switch ($search_in)
		{
			case false:
				return false;
				break;
			case 'tag':
				$result = \lib\db\terms::search($search, ['term_type' => "tag"]);
				break;
			default:
				$result = \lib\db\terms::search($search, ['term_type' => $search_in]);
				break;
		}
		$result = json_encode($result, JSON_UNESCAPED_UNICODE);
		return $result;
	}
}
?>