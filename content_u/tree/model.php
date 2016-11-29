<?php
namespace content_u\tree;
use \lib\utility;

class model extends \mvc\model
{

	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_search($_args)
	{

		if(isset($_args->match->url[0][0]) && $_args->match->url[0][0] == 'tree')
		{
			return \lib\db\polls::search(null,
				[
					'all'  	   => true,
					'get_last' => true,
					'user_id'  => $this->login('id')
				]);
		}

		$meta        = [];
		$meta['all'] = true;
		$repository  = $_args->get("repository")[0];
		switch ($repository)
		{
			case 'personal':
				$meta['user_id'] = $this->login('id');
				$meta['my_poll'] = true; // to load not published poll
				break;

			case 'sarshomar':
				$meta['post_sarshomar'] = 1;
				$meta['all']            = false;
				break;

			case 'all':
			default:
				// no thing.
				break;
		}

		$search = $_args->get("search")[0];
		$result = \lib\db\polls::search($search, $meta);

		return $result;
	}

}
?>