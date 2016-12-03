<?php
namespace content_u\add\model;

trait tree
{
	/**
	 * Gets the search.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public function get_tree($_args)
	{
		$meta          = [];
		$meta['all']   = true;
		$meta['order'] = "DESC";
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